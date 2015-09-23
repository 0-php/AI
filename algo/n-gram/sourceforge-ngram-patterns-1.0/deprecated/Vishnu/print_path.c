/**********************************************************************************************
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
**************************************************************************************************/	

/*==========================================print_path.c============================================
Aurthor: Anurag Jain
Course: CS 8621
Created: Fall 2007
Operating System: Linux
License: GNU General Public License (GPL)
================================================================================================*/



/*********************************FILE DESCRIPTION********************************************
This file consists of functions for accessing the constructed co-occurence network. 

How It Works:-
------------
1) Once the network is built. We take the a file consisting of tokens and corresponding length
   as input.
2) We pick a token and the specified length from the file and print all the paths. 
   We do this step one by one for each token till we reach the end of file.
   
   Printing All Paths
   ------------------
   a) Gathering the information from the input file and seperating the master processors work 
      from other processors. (print_query function)
   b) Once the work has been distributed the print_length_1 function recursively passes the incoming
      edge string of specified length to print_length_2. For each incoming edge print_length_2  
      recursively prints the incoming edge followed by all outgoing edges.

Other Inportant Details
-----------------------
Self-Loops
----------
Self Loops occur when same edge occurs in a path more than once.

Note: We will be skipping all the paths. containing self-loops.

Avoiding Self-Loops (Edge-Marking Method)
-----------------------------------------
In this method while building up a path to be printed we mark all the edges that have been included 
in the path so that if they occur again we can just skip them and move to the next connected edge and 
so on till we find an unmarked edge. In this was way we can avoid the self loops.

Complete Paths
--------------
If the last node in the printed path does not have any parent or a child then that path is called a 
complete path. 

Note: We will be printing such paths. It may happen that complete paths do not satisfy the specified 
length criteria i.e. that is their length is smaller than the specified length but we will still print 
them.

Printing Complete Paths
-----------------------
To print the complete paths we took advantage of the property of complete paths that the last node in 
a complete path will have no parent or no child or both. If the last node does not have any child or 
parent or both and the length is less than or equal to the specified length we print the path.
*************************************************************************************************/




/*******************************PARALLELISM DETAILS*********************************************
We have created the co-occurence network using the strength of multiple processors.
Each processor has a unigram list with it in the form of an array. Each element of the array
consists of two pointers. 
a) A pointer pointing to the linked list of outgoing edges.
b) A pointer pointing to the linked list of incoming edges.

We are reading each bigram file in parallel. Chances are that for an element in the unigram 
array the incoming and outgoing edges are going to be distributed among the multiple 
processors in use.

So, if we want to print all the paths for a particular token from the unigram array we should 
gather all the incoming as well as the outgoing edges for that token on some processor and 
then recursively print the paths.  
*************************************************************************************************/




/******************************TECHNICAL DETAILS*************************************************
1) Processor 0 is the processor chosen to store all the edges. 
2) It will be requesting other processors for the edges information and they will suffice to its 
   requests.
3) Once 0'th processor has the edge information it will print the results.  
*************************************************************************************************/




/********************************OBSERVATIONS**************************************************** 
The 0th processor may run out of memory depending upon for how many token it needs to keep the 
all the edge information. Because we can end up in a situation where the whole network resides
on the processor 0. So, we have to make sure that we have enough memory with processor zero or 
some other way out. Right now we will try to provide enough memory to processor 0.

Memory Requirements For Processor 0
-----------------------------------
a) Memory for the unigram token array. 
   ---------------------------------
   (Number of Unigrams) * (Size of node)  

b) Memory for the edges.  
   --------------------
   2 * (Number of Bigrams) * (Size of Edge) 
   Important Note:- Each edge is represented twice in the network once as an incoming edge. And
   once as an outgoing edge. That's the reason for multiplying by 2.

SYSTEM REQUIREMENTS FOR THE OVERALL SYSTEM:
------------------------------------------

Total memory requirement for each processors = 25 Gb(0th processor) + ((16.5/p) + 1.5)(p-1) Gb  
Where p = total number of processors

Preffered System: Altix 3700 BX2
----------------
Why Altix?
----------
The system might work for some target words with or without cutoff and small lengths on
IBM Blade Center but the real power of the system can only be realized on Altix 3700 BX2 
because of the tremendous memory requirement for path preinting. So to run without Signal 9 
errors because of lack of memory Altix is the system to go for.

Minimum system resource requirement for the whole system:
---------------------------------------------------------
Number of processors = 4 with following configuration
mem = 42GB, nodes=1: ppn=4

Recommended system resource requirement for the whole system: 
------------------------------------------------------------
Number of processors = 4 with following configuration
pmem = 50Gb, nodes=1: ppn=4
*************************************************************************************************/



/******************************BENCHMARKING RESULTS**********************************************
				Altix 3700 BX2
				--------------
Input Token: fountain
Mem: 50gb
Cutoff = 0

Number Of Processors		Specified Length		Time Taken To Print Query(Seconds)
        4				4	                 Job Blocked (File Size > 48 Gb)
        4                               3                        Job Blocked (File Size > 48 Gb)
        4                               2                        Job Blocked (File Size > 48 Gb)
       64                               1                               233.876060

                                IBM Blade Center 
                                ----------------
Input Token: fountain
pmem: 7gb
Cutoff = 1000

Number Of Processors            Specified Length                Time Taken To Print Query(Seconds)
        4                               2                               2985.600088 

Observation: Cutoff plays a inportant role in reducing the size of the overall network which allows
             us to print more specific paths. If we increase the cut-off to a very high value the
             number of paths printed is reduced tremendously. 
*************************************************************************************************/




//================================INCLUDE FILES======================================
#include "print_path.h"
#include "prune.h"

//=============================FUNCTION DEFINITIONS==================================
/*===================================================================================
print_query Function
--------------------
This function seperates the master processor's work from the work done by other processors.
Master processor prints the incoming, outgoing edges as well as the frequencies. For that 
it needs to collect all the incoming and outgoing nodes which are distributed among different
processors. So all the other processors except the master processor are in infinite recieving
mode (so that they can suffice to masters requests) till the master processor finishes the 
printing work. 
=======================================================================================*/

void print_query(node** temp_word, char *path, int temp_id, int temp_size, int temp_uSize, double temp_associative_cut)
{
    id = temp_id;
    p = temp_size;
    word = temp_word;
    uSize = temp_uSize;
    associativeCut = temp_associative_cut;
    //printf("associativeCut :%f\n", temp_associative_cut);
    char search_var[40];
    FILE *pFile;
    
    //Open the input file
    if((pFile=fopen(path,"r"))==NULL)
                printf("\nError opening file.\n");
    else
    {
	int i, j;
        // Get the length of input file
        j = getInputFileLength(pFile);
        rewind(pFile);

        // Run the for loop number of lines in the files times.
        for(i=0; i<j; i++)
	{
		fscanf(pFile,"%s",search_var);
                fscanf(pFile,"%d",&lengthx);

    		//Get length of the search string allocate memory and store it into search_var
		search_index = binarySearch(search_var, uSize, word);

		if(search_index == -1) //The possibility that a word requested may not appear in the network
	    	{
			if(id == 0)
			{
				 printf("\nToken: %s", search_var);
                                 printf("\nLength: %d", lengthx);
				 printf("\nToken not found.\n");
			}
		}
	    	else
    		{	
     			if(id == 0) // Work for the master processor
	     		{
		
				printf("\nToken: %s", search_var);
                                printf("\nLength: %d\n", lengthx);

				// Call to the print_length_1 function which further calls print_length_2
				print_length_1(search_index, -1, "", -1);
	
				// Abort Array and loop variable
				int arr_abort[2], vx;
				arr_abort[0] = -1;
				arr_abort[1] = -1;
			
				// Send message using abort array to all the processes other then the master 
				// time to break the infinite while lop and stop execution as the print 
				// operation has been performed.
				for(vx = 1; vx < p; vx++)
					MPI_Send(arr_abort, 2, MPI_INT, vx, 121, MPI_COMM_WORLD);
			}
		        else // Work for all the processors other than the master processors
     			{
				// Array sent by the master processor telling this processor the index for which
				// the nodes need to be sent and whether incoming or outing nodes need to be sent
				int arr_sent[2];
	
				// Variable storing the information for the master process regarding how many times
				// it should be expect to recieve nodes from this processor
		        	int sent_times_loop;

				// Array storing the index of the node and frequency that needs to be sent to the 
				// master processor.
        			int node_send_arr[2];

				// Infinite while loop (All the processors other than the master processor are in 
				// a recieving state waiting to receive a message from the master processor which
				// sends messages sequentially.
	        		while(1)
        			{
					// Recieve the index for which the nodes need to be sent and what type of
		    		        // nodes need to be sent incoming or outgoing
				        MPI_Recv(arr_sent, 2, MPI_INT, 0, 121, MPI_COMM_WORLD, &status);
	
					// The second element in the arr_sent represents whether we are concerned 
					// with outgoing or incoming nodes. Incoming represented by 10. Outgoing
					// represented by -10.
			        	if(arr_sent[1] == 10)
            				{
						// Number of times the master processors should expect to recieve.
						sent_times_loop = word[arr_sent[0]]->count_incoming;
			        	}
            				else if(arr_sent[1] == -10)
		        		{
						// Number of times the master processors should expect to recieve.
						sent_times_loop = word[arr_sent[0]]->count_outgoing;
        		    		}
				        else if(arr_sent[0] == -1 && arr_sent[1] == -1) // Break the infinite while loop
            				{
		                		break;
	            			}
        	    
				
		        		// Sending back the information how many times the master processor should
			        	// recive the sent nodes.
	        	    		MPI_Send(&sent_times_loop, 1, MPI_INT, 0, 122, MPI_COMM_WORLD);
        	    
	        		        if(arr_sent[1] == 10)
            				{
						// Send the nodes one by one
						edge *temp = word[arr_sent[0]]->incoming;
						while(temp != NULL)
        	        			{
							// Store the node into the two element array.
							node_send_arr[0] = temp->index;
			        	        	node_send_arr[1] = temp->freq;
							// Send the two element array to the master processor
        	            				MPI_Send(node_send_arr, 2, MPI_INT, 0, 123, MPI_COMM_WORLD);
					                temp = temp->next;
        	        			}
            				}
	              			else if(arr_sent[1] == -10)
	                        	{
						// Send the nodes one by one
						edge *temp = word[arr_sent[0]]->outgoing;
			                        while(temp != NULL)
                				{
							// Store the node into the two element array.
							node_send_arr[0] = temp->index;
		                        		node_send_arr[1] = temp->freq;
							// Send the two element array to the master processor
	        	            			MPI_Send(node_send_arr, 2, MPI_INT, 0, 123, MPI_COMM_WORLD);
				                        temp = temp->next;
                				}
            				}
				}
    	 		}
		}
   	}
}
fclose(pFile);
}


/*======================================================================================
print_length_1 Recursive Function
---------------------------------
This function recursive prints all the incoming paths to the queried token. For each path 
which it prints it calls the print_length_2 function which prints all the outgoing edges.  
=======================================================================================*/

void print_length_1(int search_index1, int com_freq, char *print_statement1, double assoc_freq)
{
	int n;
	// Variable to append print statements by '->'
        char *a = "->";
	// Char array used to store the printing information
        char print_statement2[1000]= "";
        // Increment global variable length1
        length1++;

        if(length1 > 0)
        {
                // Append Print Statement
                n=sprintf (print_statement2, "%s%s(%d)(%.19f)%s%s", word[search_index1]->token, a, com_freq, assoc_freq, a, print_statement1);
        }

	// Do not collect edge information for the last edge
	if(length1 < lengthx)
	{
		// Collect the incoming nodes	
		collect_incoming_nodes(search_index1);
	}

	//Current pointer of incoming linked list
        edge *temp;
        temp = word[search_index1]->incoming;

	// Complete path incoming side
	if(temp == NULL)
	{
                print_length_2(search_index, com_freq, print_statement2, assoc_freq);
		length2 = -1; // Setting length2 used by print_length_2 to -1
	}

	// Traverse the incoming list
        while(temp != NULL)
        {
	    // If the length1 is equal to the lengthx(queried_length) then its time to call the print_length_2
	    // function.
            if(length1 == lengthx)
            {
	      	  print_length_2(search_index, com_freq, print_statement2, assoc_freq);
                  length2 = -1; // Setting length2 used by print_length_2 to -1
                  length1--; // Decrementing the length1 by 1 (One level up)
                  break; // Break the loop as we have the specified length limit for this path
            }

	    // If the edge is marked or it does not follow associative cut-off skip it
            while((temp->marked < length1 && temp->marked != -1) || 
                   (!((int)(associativeCutNotSatisfied(word[search_index1]->freq, word[temp->index]->freq, temp->freq,  associativeCut)))))
            {
                 temp = temp->next;
                 if(temp == NULL)
                 	break;
            }
	   

  	    if(temp != NULL)
            {
		// Mark edge's both entries
		{
            		if(temp->marked == -1)
                	{
  	              		temp->marked = length1;
				temp->marked_before = 1;

				// Collect the outgoing nodes
				collect_outgoing_nodes(temp->index);

	                        edge* temp1;
          	                temp1 = word[temp->index]->outgoing;
                    		while(temp1->index != search_index1)
		                {
         		            temp1 = temp1->next;
                      		}
		                temp1->marked=length1;
                                temp1->marked_before = 1;
                	}
		}
		print_length_1(temp->index, temp->freq, print_statement2, associativeRatio(word[search_index1]->freq, word[temp->index]->freq, temp->freq,  associativeCut)); // Recursive call to the print_length_1
		double ass_value;
		ass_value = associativeRatio(word[search_index1]->freq, word[temp->index]->freq, temp->freq,  associativeCut);
		//printf("\nassociativeRatio %f\n", ass_value);
		// Unmark edge's both entries 
		{	
         	       if(temp->marked == length1 && temp->marked_before == 1)
                       {
                		temp->marked = -1;
                                temp->marked_before = 0;
                        	edge* temp1;
	                        temp1 = word[temp->index]->outgoing;
        	                while(temp1->index != search_index1)
                	        {
                        		temp1 = temp1->next;
                        	}
	                        temp1->marked=-1;
                                temp1->marked_before = 0;
        	        }
		}

            	temp = temp->next;
	 }
        }

        if(temp == NULL)
                length1--;
	
}



/*======================================================================================
print_length_2 Recursive Function
---------------------------------
This function recursive prints all the outgoing paths to the queried token. It is called
by the print_length_1 function.  
=======================================================================================*/
void print_length_2(int search_index2, int com_freq1, char *print_statement3, double assoc_freq1)
{
	int n;

        // Variable to append print statements by '->'
        char *a = "->";
	// Char array used to store the printing information
        char print_statement4[1000] = "";
        
        // Increment global variable length2
        length2++;

	// Do not collect edges for the last node
	if(length2 < lengthx)
	{	
		//Collect the outgoing nodes
	        collect_outgoing_nodes(search_index2);
	}

        edge *temp;
        //Current pointer of outgoing linked list
        temp = word[search_index2]->outgoing;

	// Append Print Statement
        if(length2 == 0)
                n=sprintf (print_statement4, "%s%s%s", print_statement3, word[search_index2]->token, a);
        else if(length2 != lengthx)
	{
		if(temp == NULL)
                        n=sprintf (print_statement4, "%s(%d)(%.19f)%s%s", print_statement3, com_freq1, assoc_freq1, a, word[search_index2]->token);
		else
	                n=sprintf (print_statement4, "%s(%d)(%.19f)%s%s%s", print_statement3, com_freq1, assoc_freq1, a, word[search_index2]->token, a);
	}
        else
                n=sprintf (print_statement4, "%s(%d)(%.19f)%s%s", print_statement3, com_freq1, assoc_freq1, a, word[search_index2]->token);

	// Complete path outgoing side print
	if(temp == NULL)
        {
	        printf("%s\n", print_statement4);
        }

	// Traverse the outgoing list
        while(temp != NULL)
        {
		// If the length2 is equal to the lengthx(queried_length) then its time to print.
                if(length2 == lengthx)
                {
                        printf("%s\n", print_statement4);
                        length2--; // Decrementing the length2 by 1 (One level up)
                        break; // Break the loop as we have the specified length limit for this path
                }
		
		// If the edge is marked or it does not follow associative cut-off skip it
		while((temp->marked_before == 1) || (temp->marked < length2 && temp->marked != -1) || 
                   (!((int)(associativeCutNotSatisfied(word[search_index2]->freq, word[temp->index]->freq, temp->freq,  associativeCut)))))
               	{
       	            temp = temp->next;
               	    if(temp == NULL)
                       	break;
               	}

		
                if(temp != NULL)
                {
			// Dont mark the last outgoing edge (useless)
                        if(length2 < lengthx - 1 && temp->marked_before == 0)
                        {
				// Mark the edge
                                if(temp->marked == -1)
                                {
                                        temp->marked = length2;
				        collect_incoming_nodes(temp->index);
                                        edge* temp1;
                                        temp1 = word[temp->index]->incoming;
                                        while(temp1->index != search_index2)
                                        {
                                                temp1 = temp1->next;
                                        }
                                        temp1->marked=length2;
                                }
                        }

	                print_length_2(temp->index,temp->freq, print_statement4, associativeRatio(word[search_index2]->freq, word[temp->index]->freq, temp->freq,  associativeCut)); // Recursive call to the print_length_2
 
			// Dont try to unmark last outgoing edge (never marked)
                        if(length2 < lengthx - 1 &&  temp->marked_before == 0)
                        {
				// Unmark the edge
                                if(temp->marked == length2)
                                {
                                        temp->marked = -1;
                                        collect_incoming_nodes(temp->index);
                                        edge* temp1;
                                        temp1 = word[temp->index]->incoming;
                                        while(temp1->index != search_index2)
                                        {
                                                temp1 = temp1->next;
                                        }
                                        temp1->marked=-1;
                                }
                        }
 
        	        temp = temp->next;
		}
        }
        if(temp == NULL)
                length2--;
}

/*======================================================================================
collect_incoming_nodes Function
---------------------------------
Collect the incoming edges for the specified node on processor 0.
=======================================================================================*/

void collect_incoming_nodes(int search_index1)
{
 	// Loop Variable
        int vy;
        // Differentiate between incoming and outgoing send - incoming = 10 & outgoing = -10
        int arr_incoming[2];
        arr_incoming[0] = search_index1;
        arr_incoming[1] = 10;
        int recv_times_loop;

        // We want to collect the incoming nodes for a particular index in the vocab array only
        // once in the whole process of printing.
        if(word[search_index1]->incoming_nodes_added == 0)
        {
                for(vy=1; vy<p; vy++)
                {
                        // Send the request to processor vy to send the incoming nodes for search_index1
                        MPI_Send(arr_incoming, 2, MPI_INT, vy, 121, MPI_COMM_WORLD);

                        // Receiving the no. of times processor zero should loop to receive the nodes
                        MPI_Recv(&recv_times_loop, 1, MPI_INT, vy, 122, MPI_COMM_WORLD, &status);

                        // Loop Variable
                        int v1;
                        // Array for storing the incoming nodes sent from processor vy
                        int arr_recv_incoming[2];

                        // Recieving the nodes from processor vy one by one
                        for(v1 = 0; v1 < recv_times_loop; v1++)
                        {
                                MPI_Recv(arr_recv_incoming, 2, MPI_INT, vy, 123, MPI_COMM_WORLD, &status);
                                //Add the incoming node
                                add_incoming(word[search_index1], arr_recv_incoming[1], arr_recv_incoming[0]);

                        }
                }
                // Setting the flag that all the incoming nodes have been collected for search_index1
                word[search_index1]->incoming_nodes_added = 1;
        }
}

/*======================================================================================
collect_outgoing_nodes Function
---------------------------------
Collect the outgoing edges for the specified node on processor 0.
=======================================================================================*/
void collect_outgoing_nodes(int search_index2)
{
	// Loop Variable
        int vz;
        // Differentiate between incoming and outgoing send - incoming = 10 & outgoing = -10
        int arr_outgoing[2];
        arr_outgoing[0] = search_index2;
        arr_outgoing[1] = -10;
        int recv_times_loop;


        // We want to collect the outgoing nodes for a particular index in the vocab array only
        // once in the whole process of printing.
        if(word[search_index2]->outgoing_nodes_added == 0)
        {
                for(vz=1; vz < p; vz++)
                {
                        // Send the request to processor vz to send the outgoing nodes for search_index1
                        MPI_Send(arr_outgoing, 2, MPI_INT, vz, 121, MPI_COMM_WORLD);

                        // Receiving the no. of times processor zero should loop to receive the nodes
                        MPI_Recv(&recv_times_loop, 1, MPI_INT, vz, 122, MPI_COMM_WORLD, &status);

                        // Loop Variable
                        int v1;
                        // Array for storing the outgoing nodes sent from processor vz
                        int arr_recv_outgoing[2];

                        // Recieving the nodes from processor vz one by one
                        for(v1 = 0; v1 < recv_times_loop; v1++)
                        {
                                MPI_Recv(arr_recv_outgoing, 2, MPI_INT, vz, 123, MPI_COMM_WORLD, &status);
                                //Add the outgoing node
                                add_outgoing(word[search_index2], arr_recv_outgoing[1], arr_recv_outgoing[0]);

                        }
                }
                // Setting the flag that all the outgoing nodes have been collected for search_index2
                word[search_index2]->outgoing_nodes_added = 1;
        }
}

/*======================================================================================
getInputFileLength Function
---------------------------------
Counts the number of lines in the file.
=======================================================================================*/
int getInputFileLength(FILE *pFile)
{
        char ch[100];
        int count=0;
        while(fgets(ch,100,pFile)!=NULL)
              count++;
        return count;
}

//*********************************************************************************************************
