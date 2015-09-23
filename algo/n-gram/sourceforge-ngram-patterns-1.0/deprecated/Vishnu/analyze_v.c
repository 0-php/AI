#include "analyze_v.h"
#include <mpi.h>
/*
 Copyright (C) 2007 Flamengo team.
   This file is part of the Fun with N-gram-patterns.
   
   N-gram-patterns is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   N-gram-patterns is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program. If not, see <http://www.gnu.org/licenses/>.

   Author: Vishnu Praveen Pedireddi.
   

*/

// This method is called by processor 0. This is the method that instructs all the slaves what task to be performed next.
void masterAnalyzeNetwork(node** nodePool , int size , int masterId , int numProcs )
{
   int counter;
   int index , slave; 
   int lastUnseen = 0;
   int networkId = 0;
   int outcounter;

  
   //for(outcounter = 0 ; outcounter < 2 ; outcounter++)    

   // This loop makes sure every node actually belongs to a network.
   // Every loop signifies a network.
   // therefor the first loop searches and finds the first network.
   while(1)
   {
     networkId++;                                                     // Determing the network id of the network being currently checked. 
     index = lastUnseen;                                              // Holds the index of the word that represents a new network.
     checkAllBranch(nodePool[index] , nodePool , networkId , index , masterId , numProcs);
                                                                     // Checks for the children of a given word under consideration.
     /*

        The functionality of the while loop is explained below.
	
	1. After the node is actually broadcasted by master to all the slaves, the newly discovered nodes in the slaves need to be known to all
	   other processors.
         
	2. Therefore, broadcast the newly found nodes from the slaves to all other processors which is done by the method slavesBroadcast.

        3. Then a check is performed local to every processor for the discovery of of new nodes by other slaves. This is performed by method localCheck 	      localCheck returns a value. 
	
	4. It has to kept in mind that we have to check for the discovery of new nodes in the other slaves too. Therefore, the master instructs for a 
           localized check on the slaves too.
        5. This method that instructs the slaves to do so is done by localCheck.

        6. This loop is continued until new nodes are found in the network being searched.	
	
     */
      while(1)
      //for(counter = 0 ; counter < 2 ; counter++)
      {
        slavesBroadcast(numProcs , nodePool , size);  


        broadcast_Message(- 4 , masterId , numProcs);
        recurseAgain = 0;   
        recurseAgain = localCheck(nodePool , masterId, size , networkId);
   
        //printf("\n Master 1 : The value of recurseAgain is %d ", recurseAgain); 
   

        if(recurseAgain == 0)
        {
          broadcast_Message( - 5 , masterId , numProcs);
          recurseAgain = slavesNewNodeCheck(numProcs);
         
        }
        //printf("\n Master 2 : The value of recurseAgain is %d ", recurseAgain); 
   

       if(recurseAgain == 0)
         break;


      }

      lastUnseen = nextNode(nodePool , size , lastUnseen);
      if(lastUnseen == -1)
        break;
       

  }
  
  
   
     
   /* 
       After every node is searched and assigned a network id, the master instructs the slave to stop its execution.

      */
 
   // End the program :)
   broadcast_Message( -3 , masterId , numProcs);

  for(counter = 0 ; counter < size ; counter++)
   {
      //printf("\n The value of has_seen in the master for %s is %d \n", nodePool[counter]->token , nodePool[counter]->has_seen);
      //printf("\n The value of is_checked in the master for %s is %d \n", nodePool[counter]->token ,  nodePool[counter]->is_checked);
   } 

  printf("\n The total number of networks are %d\n", networkId);   
 /* while(1)
    {     
      lastUnseen =  nextNode(nodePool , size, lastUnseen);
       
       if(lastUnseen == -1)
        break;
       //printf("\n The index of the next node to be selected is %d with token %s\n", lastUnseen, nodePool[lastUnseen]->token);
   
     
    } */

   printf("\n The control has reaced outside the while loop ");      
    


}

// This method performs a Depth First Search on a chosen word.

void checkAllBranch(node* _node, node** nodePool , int networkId , int index , int masterId , int numProcs)
{
  
    int counter;
  	
    // has_seen a variable in a structure that holds the information as which node belongs to a given network.
      if (_node->has_seen != networkId)
	{

	// Broadcast the word being traversed in the master to every other slave.
		// This broadcast is done only by the master and not by the slave.
          if(masterId != -1)
         {
           // printf(" The broadcasted values are: %s with %d as master ", node_pool[i]->token , _master_id);
           broadcast_Message(networkId, masterId , numProcs);
           broadcast_Message(index, masterId, numProcs); 
           //printf(" The value being broadcasted is %s ", _node->token);
         }

                _node->has_seen = networkId;
		edge* temp;
		temp = _node->incoming;
		// If there are any parents to the current node to the node being searched, search them.
		while (temp != NULL)
		{
		  //printf("INCOMMING: %s <- %s  ",_node->token,node_pool[temp->index]->token);
			if (nodePool[temp->index]->is_checked == 0)
			  {
			    checkAllBranch(nodePool[temp->index] , nodePool , networkId, temp->index , masterId , numProcs);
                                
                          }
			temp = temp->next;
		}
		// If there are any children to a node being searched, search them. 
		temp = _node->outgoing;
		while (temp != NULL)
		{
		  //printf(" OUTGOING: %s -> %s ",_node->token,node_pool[temp->index]->token);
			if (nodePool[temp->index]->is_checked == 0)
			  {
                           checkAllBranch(nodePool[temp->index] , nodePool , networkId, temp->index , masterId , numProcs);
                          
                                   
                          }
			temp = temp->next;
		}

             
            _node->is_checked = networkId;
	   
	}
}

// This method is called by every slave. The slave performs a task as instructed by the master.
/*
   The following are the tasks which are done by the slave. This is performed by the value that is sent by the master.
   The following numbers signify the following tasks.

   -3:  end the loop and every slave exits.

   -2: A slave is assigned the control to broadcast its values over to the othe nodes.
 
   -4: Perform a local check for the discovery of new nodes by the other slaves.

   -5: Send message to the master about if there are any new nodes to be travered.

   for all others:
       Receive the word and perform a DFS in the slaves.
   */
void slaveAnalyzeNetwork(node** nodePool , int size , int id , int numProcs)
{

   int counter , nextTask = 0; 
   MPI_Status status;   
   int index , tempMaster , broadcastIndex;
   node* broadcastNode; 
   int broadcastLoopbreak;

   while(1)
   {
      MPI_Recv(&nextTask , 1 , MPI_INT , MPI_ANY_SOURCE , MPI_ANY_TAG , MPI_COMM_WORLD , &status); 
     
      switch(nextTask)
      {
    
         case -3:
          goto programEnd;  
          break;

         case -2:
          
        MPI_Recv(&tempMaster , 1 , MPI_INT , MPI_ANY_SOURCE , MPI_ANY_TAG , MPI_COMM_WORLD , &status);
        broadcastLoopbreak = 0;
        if( id == tempMaster)
        {
         
         for(counter = 0 ; counter < size ; counter++)
          {
            if(nodePool[counter]->has_seen == slaveNetworkId && nodePool[counter]->is_checked == slaveNetworkId)
            {

               MPI_Barrier(MPI_COMM_WORLD);
               broadcastIndex = counter;
               MPI_Bcast(&broadcastLoopbreak , 1 , MPI_INT , id , MPI_COMM_WORLD);
               MPI_Bcast(&broadcastIndex , 1, MPI_INT , id , MPI_COMM_WORLD);
               
               broadcastNode = nodePool[broadcastIndex];
               //printf("\n From Slave: %s: %d ", broadcastNode->token, broadcastNode->has_seen);
               //printf("\n From Slave: id: %d ", id);
               MPI_Bcast(&broadcastNode->has_seen , 1 , MPI_INT , id , MPI_COMM_WORLD);
                
            }
                 
          }
          
          MPI_Barrier(MPI_COMM_WORLD);
	  broadcastLoopbreak = 1;
	  MPI_Bcast(&broadcastLoopbreak , 1 , MPI_INT , id , MPI_COMM_WORLD);
	     
         }
        else
        {
          //for(counter = 0 ; counter < size ; counter++)
                  while(1)
                  {
                    MPI_Barrier(MPI_COMM_WORLD);
                    MPI_Bcast(&broadcastLoopbreak , 1 , MPI_INT , tempMaster , MPI_COMM_WORLD);
                    if(broadcastLoopbreak == 1)
                      break;
                    MPI_Bcast(&broadcastIndex , 1 , MPI_INT , tempMaster , MPI_COMM_WORLD);
                    broadcastNode = nodePool[broadcastIndex];
                    MPI_Bcast(&broadcastNode->has_seen , 1 , MPI_INT , tempMaster , MPI_COMM_WORLD);
                  
              
                   }
                  
         } 

        MPI_Barrier(MPI_COMM_WORLD);
        
         break;

         case -4:
          
           recurseAgain = 0;
           recurseAgain = localCheck(nodePool, -1 , size , slaveNetworkId );
           //printf("\nSlave id: %d , recurseAgain :  %d ", id , recurseAgain);

         break;

         case -5:
           
           MPI_Send(&recurseAgain , 1 , MPI_INT , 0 , 0 , MPI_COMM_WORLD);
          
         break;
         
  
         default:
          //printf(" From Slave: The network id is %d \n", nextTask);
          slaveNetworkId = nextTask;
          MPI_Recv(&index , 1 , MPI_INT , MPI_ANY_SOURCE , MPI_ANY_TAG , MPI_COMM_WORLD , &status);
          
          // After receiving the network id and index to be checked, traverse the graph.
          checkAllBranch(nodePool[index] , nodePool , nextTask , index , -1 , numProcs);
         
         // printf(" From Slave: The word broadcased by master is %s ",nodePool[index]->token); 
          break;
       }

    }

    programEnd:
    /*for(counter = 0 ; counter < size ; counter++)
      {
     
       //printf("\n The value of has_seen for %s in slave is %d ", nodePool[counter]->token , nodePool[counter]->has_seen);

      }*/

}

// Broadcast a number to every slave from the master. This number is an integer.
void broadcast_Message(int _content , int _master_id , int _numProcs)
{
  int counter;
  for(counter = 0 ; counter < _numProcs ; counter++)
    {
      if(counter == _master_id)
	continue;
      else
	{
	  MPI_Send(&_content,1,MPI_INT,counter,0,MPI_COMM_WORLD);
        }  
 
    }
}

// This method broadcasts the newly found nodes to every other processor.
void slavesBroadcast(int numProcs , node** nodePool, int size)
{
  int slave , counter;
  int value = -2 , dummy;
  MPI_Status status;
  node* broadcastNode;
  int broadcastLoopbreak , broadcastIndex;   
  
  for(slave = 1 ; slave < numProcs ; slave++)
  {
    //MPI_Send(&value , 1 , MPI_INT , slave , 0 , MPI_COMM_WORLD);
    broadcast_Message(-2 , 0 , numProcs);
    broadcast_Message(slave , 0 , numProcs);
    //for(counter = 0 ; counter < size ; counter++)
     while(1) 
      {
        MPI_Barrier(MPI_COMM_WORLD);
        MPI_Bcast(&broadcastLoopbreak , 1 , MPI_INT , slave , MPI_COMM_WORLD);
        if(broadcastLoopbreak == 1)
           break;
        MPI_Bcast(&broadcastIndex , 1 , MPI_INT , slave , MPI_COMM_WORLD);

        broadcastNode = nodePool[broadcastIndex]; 
        MPI_Bcast(&broadcastNode->has_seen , 1 , MPI_INT , slave , MPI_COMM_WORLD);
      }        
    MPI_Barrier(MPI_COMM_WORLD);
    //MPI_Recv(&dummy , 1 , MPI_INT , slave , MPI_ANY_TAG , MPI_COMM_WORLD , &status);
  }

 
  /*for(slave = 1 ; slave < numProcs ; slave++)
  {
   broadcast_Message(slave , 0 , numProcs);
   receiveFromSlave(nodePool);
   MPI_Recv(&dummy , 1 , MPI_INT , slave , MPI_ANY_TAG , MPI_COMM_WORLD , &status);
  } */
 
}


// This method checks for newly discovered nodes in the processor.
int localCheck(node** nodePool , int masterId, int size, int networkId)
{
// The masterId is 0 for processor 0 and and the masterId is -1 for slaves.

 //printf("\n From masterId: %d ", masterId);
 int counter;
 int runAgain = 0;
 for(counter = 0 ; counter < size ; counter++)
 {
    if(nodePool[counter]->has_seen == networkId && nodePool[counter]->is_checked == 0)
    {
      //printf(" \nYahoo!!! From Processor: %d  token : %s ", masterId , nodePool[counter]->token);
      checkAllBranch2(nodePool[counter], nodePool , networkId , counter , -1 , 0);
      runAgain = 1;
        
    }
 } 

 return runAgain;
}


// Used for traversal of tree locally by the master and the slave.
void checkAllBranch2(node* _node, node** nodePool , int networkId , int index , int masterId , int numProcs)
{
  
 
  int counter;
 
                //_node->has_seen = networkId;
		edge* temp;
		temp = _node->incoming;
		while (temp != NULL)
		{
		  //printf("INCOMMING: %s <- %s  ",_node->token,node_pool[temp->index]->token);
			if (nodePool[temp->index]->is_checked == 0)
			  {
			    checkAllBranch(nodePool[temp->index] , nodePool , networkId, temp->index , masterId , numProcs);
                                
                          }
			temp = temp->next;
		}
		temp = _node->outgoing;
		while (temp != NULL)
		{
		  //printf(" OUTGOING: %s -> %s ",_node->token,node_pool[temp->index]->token);
			if (nodePool[temp->index]->is_checked == 0)
			  {
                           checkAllBranch(nodePool[temp->index] , nodePool , networkId, temp->index , masterId , numProcs);
                          
                                   
                          }
			temp = temp->next;
		}
            _node->is_checked = networkId;
         
}

// This method checks for newly marked nodes in the slaves which need to be traversed.
int slavesNewNodeCheck(int numProcs)
{
  int counter;
  int recvValue=0;  
  int repeatAgain = 0;
  MPI_Status status;

  for(counter = 1 ; counter < numProcs ; counter++)
     {
        MPI_Recv(&recvValue , 1 , MPI_INT , MPI_ANY_SOURCE , MPI_ANY_TAG , MPI_COMM_WORLD , &status);
        if(recvValue == 1)
         if(repeatAgain == 0)
           repeatAgain = 1;
     }
  return repeatAgain;


}
         
// This method selects the next node to be traversed. This signifies a new network. This method is called by master.
int nextNode(node** nodePool , int size, int lastSeen)
{

  int counter;
  int returnValue;
  int flag = 0;
    for(counter = lastSeen+1 ; counter < size ; counter++)
       {
         //if(nodePool[counter]-> has_seen == 0 && nodePool[counter]->is_checked == 0)
         if(nodePool[counter]-> has_seen == 0)
         {
           flag = 1;
           returnValue = counter;
           break;
         }

       }
    if(flag == 1)
      return returnValue;
    else
      return -1;
}  
              
          
