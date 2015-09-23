/*********************************************************************************
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
***********************************************************************************/

/*********************************************************************************
	CS8621- Advanced Computer Architecture
	Project Co-occurrence network creation code file
	Programer Name: Darshan Paranjape
**********************************************************************************/


/***********************************************************************************************
FILE DESCRIPTION:

This file includes functions required to build the co-occurence network.

Following are the steps involved in building co-occurrence network.

Step 1: Count the number of unigrams.
	 We're not hard coding the number of unigram so we can work on test data set. 
	 In this step, the whole unigram file is read to count the number of lines. 
	 The number of lines in this file gives us the total number of unigrams. It is 
	 necessary to find this size first so we can allocate memory to array of node 
	 structures. This step is implemented in function "count_unigram_size".


Step 2: Memory Allocation.
	 In this step, we allocate array of node structure with size equal to number of 
	 unigrams. Also the structure variables are initialized to proper values during 
	 this step. This step is implemented in function "initialize_word".


Step 3: Read the unigram file.
	 In this step, unigram file is read again to store information to the node structure. 
	 Every unigram is stored in the 'token' field of the node structure. Also 'index' 
	 field stores the index of the unigram in array of node structures.
	 This step is implemented in function "read_unigram_data".


Step 4: Bi-gram File Distribution.
	 The size & number of Bi-gram files is very high. Also while reading the file; we 
	 need to store the edge information which is going to take considerable amount of time. 
	 So we decided to implement parallelism in this step. The main idea is to assign each 
	 processor some number of lines in a file instead of making each processor read the whole 
	 file. After this calculation, every processor gets the starting line & ending line for 
	 each Bi-gram file. Every processor skips the lines in file until it reaches starting line. 
	 It reads the files & processes data only until it reaches ending line. 
	 This step is implemented in function "build_network".


Step 5: Finding index from Unigram array
	 After reading both the Bi-gram words on a line, we need to find their indices in the 
	 Unigram Array. With this indices associated with word, it can be search faster in network 
	 analysis. Finding index from array of large size (13 million approx) with liner search is 
	 method is very time consuming. We are using binary search algorithm with time complexity 
	 O(log (n)). A binary search algorithm is a technique for finding a particular value in a 
	 sorted list. We can make use of this algorithm as token in the node structure array are in 
	 alphabetical order. 
	 This step is implemented in function "binarySearch".

Step 6: Adding incoming and outgoing edge information
	 After reading 2 words from a line and finding their index, we need to store incoming and 
	 outgoing edge information. We store the index of the second word & weight of the edge in 
	 the outgoing edge linked list of first word. Similarly index of the first word & weight of 
	 the edge is stored in incoming linked list of second word. Every processor does this for its 
	 allocated number of lines. 
	 This step is implemented in functions "add_incoming" & "add_outgoing" resp.

****************************************************************************************************/

/***************************************************************************************************
PARALLEL ALGORITHM DESCRIPTION:

While creating the Co-occurrence network for Bi-gram data, we are making use of both 
Unigram and Bi-gram data files. The main idea behind this is to maintain array of all 
unigram tokens which will be used as reference while reading Bi-gram data. This method
proves useful when Unigram cutoff other 200 is specified.

The main implementation of parallelism is in Bi-gram File Distribution step. The idea 
is to assign each processor some number of lines in a file instead of making each 
processor read the whole file. We've used block allocation method for this.We first 
count the number of lines in a file. Then we count lower bound, upper bound & block size
for each processors. The lower bound is the starting line in a file whereas upperbound 
is ending line in a file for every processor. Each processors reads only block size lines 
starting from line #lower bound upto line# upper bound.
**************************************************************************************************/


/*************************************************************************************************
SYSTEM REQUIREMENTS:

Total memory requirement for each processors = (14/p) + 1.2 GB
Where p = total number of processors

So minimum system resource requirement for network creation stage is
Number of processors = 4 with following configuration 
pmem =5 GB, nodes=4: ppn=1

Recommended system resource requirement are
Number of processors = 8 with following configuration
pmem =3 GB, nodes=4: ppn=2

*************************************************************************************************/


/************************************************************************************************
BENCHMARKING RESULTS:

Time Required to build the co-occurrence network.

# of Bi-gram Files	Processors    Memory required per Processor  	Wall Clock Time(using MPI_Wtime)

	32		   8			2.95 GB			478.24972 Seconds
	32		  16			2.075 GB		325.36590 Seconds
	32		  32			1.6375 GB		236.64958 Seconds
   
*************************************************************************************************/
 

#include "network.h"

/*********************************************************************************
    Function : build_network
    Arguments: path to data,unigram cutoff limit,pointer to array of node structure
		 processor id, number of processors
    Return Value:pointer to array of node structure

    
    Function Description:
    This function builds the co-occurence network on Bi-gram data. It calls the 
    functions for reading unigram & bigram data, memory allocation for node structures
    storing incoming outgoing edge information. 
 **********************************************************************************/
node** build_network(char* path,int n,node** word,int id,int p)
{
	//Variable Declaration

	int i,j,k,a,sp;
	int size=0,biSize=0,cutoffSize;
	int lowerBound,upperBound,blockSize;
	int index1=0,index2=0;
	int uSize=0; // Size of the Unigram Array

	long int val=0;
	
	FILE *fpbi;

	char ch[40],ch1[40],ch_bigram[100],files[100];
	char* fname;

	uSize=count_unigram_size(path,n);
        //cutoffSize = getUnigramSize(path ,(long int) n);
       
        
	if(id==0)
		printf("Total Number of Unigrams in Vocab file = %d\n\n",uSize);

       word = read_unigram_data(path,n,word);
       cutoffSize = getUnigramSize(path , (long int) n);

	// File Reading Loop.
	//In this loop, data from files 2gm-0000 to 2gm-0031 are read
	//to create the co-occurrence network for whole Bi-gram data. 
	for(a=0;a<32;a++)
	{	
		biSize=0;
	
		if(a<10)
			fname="/dvd1/data/2gms/2gm-000";
		else
			fname="/dvd1/data/2gms/2gm-00";

		sp=sprintf(files,"%s%s%d",path, fname, a);


		//Counting the number of entries(lines) in Bi-gram file.
		biSize=count_bigram_size(files);

		if(id==0)
			printf("\nNumber of Bigrams in file %d = %d",a+1,biSize);


		//Block Allocation Calculation
		lowerBound=(id*biSize)/p;
		upperBound=(((id+1)*biSize)/p)-1;
		blockSize=(upperBound-lowerBound)+1;

		
		//Reading Bi-gram file & storing incoming & outgoing edge information.
		if((fpbi=fopen(files,"r"))==NULL)
			printf("\nError opening file.\n");
		else
		{
			size=0;
			
			//Skip lines to come to the lower bound
			for(i=0;i<lowerBound;i++)
				fgets (ch_bigram , 100 , fpbi);
			//Read only block number of lines
			while(size<blockSize)
			{	
				fscanf(fpbi,"%s",ch);
				fscanf(fpbi,"%s",ch1);
				fscanf(fpbi,"%ld",&val);
					
				size++;

				//Here we make use of the fact that first word on consecutive 
				//line could be same. so we don't have to find it's index again.
                        

                                /**********USED FOR CUTOFF FREQUENCY************/
                                if(notValidIndex(index1, cutoffSize))
                                 {
                                    printf("Invalid Index \n");
                                    continue;                                  
                                 }
                                /**********USED FOR CUTOFF FREQUENCY**********/


                                if(strcmp(word[index1]->token,ch)==0)
				    {
				  	 index2 = binarySearch(ch1,cutoffSize,word);
                                         
                                         /**********USED FOR CUTOFF FREQUENCY********/

                                         if(notValidIndex(index2 , cutoffSize))
                                               continue;
                                                  
                                         /**********USED FOR CUTOFF FREQUENCY*******/


                                         add_outgoing(word[index1], val, index2);
					 word[index1]->count_outgoing++;
					 word[index1]->total_out_weight+=val;

					 add_incoming(word[index2], val, index1);
					 word[index2]->count_incoming++;
				    }
				   else
				    {
					index1 = binarySearch(ch,cutoffSize,word);
					index2 = binarySearch(ch1,cutoffSize,word);
                       
                                        /***********USED FOR CUTOFF FREQUENCY********/                 

                                        if(notValidIndex(index1 , cutoffSize))
                                         {
                                            index1 = 0;
                                            continue;
                                         }
                                        if(notValidIndex(index2 , cutoffSize))
                                            continue;

                                        /***********USED FOR CUTOFF FREQUENCY*******/
					
                                        add_outgoing(word[index1], val, index2);
					word[index1]->count_outgoing++;
					word[index1]->total_out_weight+=val;

					add_incoming(word[index2], val, index1);
					word[index2]->count_incoming++;
				    }

			}
		}
		rewind(fpbi);
		fclose(fpbi);
	}
	return word;
}



/*********************************************************************************
    Function : initialize_word
    Arguments: Size of the unigram array
    Return Value:pointer to array of node structure
    
    Function Description:
    This function allocates the memory for the object of structure "node" and 
    initializes structure variables.
**********************************************************************************/

node** initialize_word(node** word,int uSize)
{
	int i;

	//Allocate memory for whole array.
	word =(node **)malloc(uSize*sizeof(node*));

        //printf(" The memory allocated to store the unigram array is %d ",uSize);
	//Allocate memory & initialize each array element
	for(i=0;i<uSize;i++)
	{
		word[i]=(node *)malloc(sizeof(node));

		word[i]->incoming=NULL;
		word[i]->outgoing=NULL;

		word[i]->curr_incoming=NULL;
		word[i]->curr_outgoing=NULL;

		word[i]->count_outgoing=0;
		word[i]->count_incoming=0;

		word[i]->has_seen=0;
		word[i]->is_checked=0;

		word[i]->total_out_weight=0;

		word[i]->incoming_nodes_added=0;
		word[i]->outgoing_nodes_added=0;
	}
	return word;
}


/*********************************************************************************
    Function : read_unigram_data()
    Arguments: path to data, lower bound on unigram
    Return Value:size of unigram array
    
    Function Description:
    This function counts the number of lines in unigram file. Calls initialize_word
    to initialize node array object.Then stores the unigrams in structure variable. 
**********************************************************************************/

node** read_unigram_data(char* path, long int n,node** word)
{
	int i,k=0,sp,id,uSize,count=0 , cutoffSize;
	long int j;
	FILE *fpuni;
	char ch[50];
	char* fname;
	char files[100];

	fname="/dvd1/data/1gms/vocab";

	sp=sprintf(files,"%s%s",path, fname);

	//Count the number of entries from unigram file whose
	//frequency is greater than cutoff limit.
	uSize=count_unigram_size(path,n);

        //printf(" The size of cutoff from input is %d\n",n);

        cutoffSize = getUnigramSize(path ,(long int) n);
//        printf(" The number of words that are greater than or equal to cut-off size is %ld \n", cutoffSize);

	
	//File Reading to create "word" array.
	//Only those entries from unigram are stored in "word" array
	//whose frequency is greater than unigram cutoff limit.

	if((fpuni=fopen(files,"r"))==NULL)
                printf("\nError opening file.\n");
       else
        {
		word = initialize_word(word, cutoffSize);

              for(i=0;i<uSize;i++)
              {
                	fscanf(fpuni,"%s",ch);
			fscanf(fpuni,"%ld",&j);
		        if(checkWord(j,(long int)n))
                        {
                	  word[count]->token=(char *)malloc(sizeof(char)*strlen(ch));
                	  strcpy(word[count]->token,ch);
		 	  word[count]->freq=j;
			  word[count]->index=count; 
			  count++;                      
                        }
	      }
	}
        //printf("\nThe size of memory allocated is %d ", count);
	fclose(fpuni);
	return word;
}

/*********************************************************************************
    Function : add_incoming
    Arguments: "node" object, weight of the edge, index of incoming words
    Return Value:None
    
    Function Description:
    This function handles the incoming linked list implementation by adding edge 
    information.
**********************************************************************************/

void add_incoming(node* wd, long int frequency,int index)
{
	edge* temp=(edge *)malloc(sizeof(edge));
	temp->freq=frequency;
	temp->index=index;
	temp->marked=-1;
	temp->marked_before=-1;
	temp->next=NULL;

	if(wd->incoming==NULL)
	{
		wd->incoming=temp;
		wd->curr_incoming=temp;
	}
	else
	{
		wd->curr_incoming->next=temp;
		wd->curr_incoming=temp;
	}
	
}



/*********************************************************************************
    Function : add_outgoing
    Arguments: "node" object, weight of the edge, index of outgoing words
    Return Value:None
    
    Function Description:
    This function handles the outgoing linked list implementation by adding edge 
    information.
**********************************************************************************/

void add_outgoing(node* wd, long int frequency,int index)
{
	edge* temp=(edge *)malloc(sizeof(edge));
	temp->freq=frequency;
	temp->index=index;
	temp->marked=-1;
	temp->marked_before=-1;
	temp->next=NULL;

	if(wd->outgoing==NULL)
	{
		wd->outgoing=temp;
		wd->curr_outgoing=temp;
	}
	else
	{
		wd->curr_outgoing->next=temp;
		wd->curr_outgoing=temp;
	}
}

/*********************************************************************************
    Function : binarySearch
    Arguments: string to be searched,size of word array,pointer to word array
    Return Value:string's index in unigram array(word array)
    
    Function Description:
    This function performs binary search algorithm to find index in unigram(word)array. 
**********************************************************************************/

int binarySearch(char* ch,int uSize, node** word)
{
	int low,mid,high;

	low=0;
	high=uSize-1;
	
	mid=(low+high)/2;

	while(low<=high)
	{
		if(strcmp(word[mid]->token,ch)==0)
		{
			return mid;
		}
		if(strcmp(word[mid]->token,ch)>0)
			high=mid-1;
		else if(strcmp(word[mid]->token,ch)<0)
			low=mid+1;
		
		mid=(low+high)/2;
	}
       
       return -1;
        
}

/*********************************************************************************
    Function : count_unigram_size
    Arguments: path to unigram file, Unigram cutoff limit
    Return Value:size of unigram array
    
    Function Description:
    This function returns number of unigrams 
**********************************************************************************/

int count_unigram_size(char* path,int n)
{
	FILE *fpuni;
	char ch[100];
	int count=0,sp,j;
	char* fname;
	char files[100];

	fname="/dvd1/data/1gms/vocab";
	sp=sprintf(files,"%s%s",path, fname);

	if((fpuni=fopen(files,"r"))==NULL)
                printf("\nError opening file.\n");
       else
       {
		while(fgets(ch,100,fpuni)!=NULL)
		{
			count++;
		}
	}
	return count;
}


/*********************************************************************************
    Function : count_bigram_size
    Arguments: path to bigram file
    Return Value:size of unigram array
    
    Function Description:
    This function returns number of entries(lines) in bi-gram file. 
**********************************************************************************/

int count_bigram_size(char* files)
{
	FILE *fpbi;
	char ch_bigram[100];
	int biSize=0;

	if((fpbi=fopen(files,"r"))==NULL)
		printf("\nError opening file.\n");
	else
	{
		//Opening the file & count number of lines in it. 
		while(fgets(ch_bigram,100,fpbi)!=NULL)	
		{	
			biSize++;
		}
	}
	fclose(fpbi);
	return biSize;
}


/*********************************************************************************
    Function : display
    Arguments: pointer to array of node structure, size of unigram array
    Return Value: none
    
    Function Description:
    This function is written for the testing purpose. While creating the network on
    small sample files, we can call this function to print information of each node 
    present in the network. 
**********************************************************************************/

void display(node** word,int uSize)
{
	int i=0;
	edge* temp;

	for(i=0;i<uSize;i++)
	{
		printf("\n%s\tIncoming=%d\tOutgoing=%d\n",word[i]->token,word[i]->count_incoming,word[i]->count_outgoing);
		temp=word[i]->outgoing;
		while(temp!=NULL)
		{
			printf("%s\t%d\n",word[temp->index]->token,temp->freq);
			temp=temp->next;
		}
	}
}

