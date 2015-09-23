/*********************************************************************************
	CS8621- Advanced Computer Architecture
	Project Alpha Stage
	Programer Name: Darshan Paranjape
**********************************************************************************/

/************************************************************************************
PROBLEM STATEMENT:

To build and use a co-occurrence network from the Google N-gram Data. In alpha stage, 
words that occur together in a Google 2-gram should be linked together, assuming that 
each of the word occurs individually 200 or more times. Approach to this problem should 
be parallel, program should run faster by increasing numbers of processors. 
************************************************************************************/

/************************************************************************************
FILE DESCRIPTION:

This file includes functions required to build the co-occurence network. It also 
includes the main function. Following are the steps involved in building co-occurrence 
network.
Step 1: Read the unigram file.
	 This step is implementes in function "read_unigram_data"

Step 2: Memory Allocation to object of the "node" structure.
	 This step is implementes in function "initialize_word"

Step 3: Bi-gram File Distribution.
	 This step is implementes in function "main"

Step 4: Finding index from Unigram array
	 This step is implementes in function "binarySearch"

Step 5: Adding incoming and outgoing edge information
	 This step is implemented in functions "add_incoming" & "add_outgoing" resp.

************************************************************************************/

/***********************************************************************************
PARALLEL ALGORITHM DESCRIPTION:

While creating the Co-occurrence network for Bi-gram data, we are making use of both 
Unigram and Bi-gram data files. The main idea behind this is to maintain array of all 
unigram tokens which will be used as reference while reading Bi-gram data. 

The main implementation of parallelism is in Bi-gram File Distribution step. The idea 
is to assign each processor some number of lines in a file instead of making each 
processor read the whole file. We've used block allocation method for this.We first 
count the number of lines in a file. Then we count lower bound, upper bound & block size
for each processors. Each processors reads only block size lines starting from
line #lower bound upto line# upper bound.
************************************************************************************/

/**************************************************************************************
BENCHMARKING RESULTS:

Time Required to build the co-occurrence network.

Processors    #of Bi-gram files         Wall Clock Time(using MPI_Wtime)

   8               12     			789 Seconds
   16              12      			525 Seconds
   32 		     32				2640 Seconds	
   
***************************************************************************************/ 


/**************************************************************************************
Technical Difficulties:
We have run this file to build the network on whole set of 32 files. It runs properly 
& builds the network. We also have graph_analysis.c & analysis.c files to count the number 
of distinct networks. We have tested those files on a sample data set. These files are also
running fine. But due to time limitation we were not able to integrate it.
***************************************************************************************/

#include<mpi.h>
#include "network.h"
#include "hash.h"
int uSize=0; // Size of the Unigram Array

int main(int argc,char *argv[])
{
	// Variable Declaration 
	int i,j,k,val=0,size=0,biSize=0,a;
	int sp;
	FILE *fpbi;
	char ch[40];
	char ch1[40];
	char ch_bigram[100];
	char ch_name[100];
	char* fname;
	FILE *fp;
	init_hash_arr();
	char files[100];
	
	char buff[10];
	int index1=0,index2=0;
	
	double elapsed_time;		/*Benchmarking*/

	int lowerBound,upperBound,blockSize;

	int* global_results;


	/*MPI Initialization*/
	MPI_Init(&argc , &argv);

	/*Getting process id/Rank and total number of processes*/
	MPI_Comm_rank (MPI_COMM_WORLD, &id);
	MPI_Comm_size (MPI_COMM_WORLD, &p);

	/*Barrier Synchronization*/
	MPI_Barrier (MPI_COMM_WORLD);
	elapsed_time= -MPI_Wtime();

       read_unigram_data(argv[1]);


	// File Reading Loop 
	for(a=0;a<1;a++)
	{	
		biSize=0;
	
		if(a<10)
			fname="/dvd1/data/2gms/2gm-000";
		else
			fname="/dvd1/data/2gms/2gm-00";
		
		sp=sprintf(files,"%s%s%d",argv[1], fname, a);

		if((fpbi=fopen("files","r"))==NULL)
			printf("\nError opening file.\n");
		else
		{
			//Opening the file & count number of lines in it. 
			while(!feof(fpbi))//while(fgets(ch_bigram,100,fpbi)!=NULL)//
			{	
				fgets (ch_bigram , 100 , fpbi);
				biSize++;
			}
		}
		rewind(fpbi);
		fclose(fpbi);

		//Block Allocation Calculation
		lowerBound=(id*biSize)/p;
		upperBound=(((id+1)*biSize)/p)-1;
		blockSize=(upperBound-lowerBound)+1;

		
		//Reading Bi-gram file & storing incoming & outgoing edge information.
		if((fpbi=fopen("files","r"))==NULL)
			printf("\nError opening file.\n");
		else
		{
			size=0;
			
			//Skip lines to come to the lower bound
			for(i=0;i<lowerBound;i++)
				fgets (ch_bigram , 100 , fpbi);

			while(size<blockSize)//&&(!feof(fpbi)))
			{	
				fscanf(fpbi,"%s",ch);
				fscanf(fpbi,"%s",ch1);
				fscanf(fpbi,"%d",&val);
				size++;

				//Here we make use of the fact that first word on consecutive 
				//line could be same. so we don't have to find it's index again.
				/*
				if(strcmp(word[index1]->token,ch)==0)
				{
					index2 = find_index(ch1, word);
				
					add_outgoing(word[index1], val, index2);
					word[index1]->count_outgoing++;
					word[index1]->total_weight+=val;

					add_incoming(word[index2], val, index1);
				}
				else*/ 
				{
					index1 = find_index(ch, word);
					printf("Word 1 %d | ", index1);
					index2 = find_index(ch1, word);
					printf("Word 2 %d\n", index2);
					add_outgoing(word[index1], val, index2);
					word[index1]->count_outgoing++;
					word[index1]->total_weight+=val;
			
					add_incoming(word[index2], val, index1);
				}
			}
		}
		rewind(fpbi);
		fclose(fpbi);
	}
	
	MPI_Barrier (MPI_COMM_WORLD);
	
	if(id==0)
	{
		elapsed_time += MPI_Wtime();
		printf("\n\nTime to build network= %lf Seconds",elapsed_time);
		fflush(stdout);
	}

	MPI_Finalize();
	return 0;
}


/*********************************************************************************
    Function : initialize_word
    Arguments: Size of the unigram array
    Return Value:None
    
    Function Description:
    This function allocates the memory for the object of structure "node" and 
    initializes structure variables.
**********************************************************************************/

void initialize_word(int size)
{
	int i;

	word =(node **)malloc(size*sizeof(node*));

	for(i=0;i<size;i++)
	{
		word[i]=(node *)malloc(sizeof(node));
		word[i]->incoming=NULL;
		word[i]->curr_incoming=NULL;
		word[i]->outgoing=NULL;
		word[i]->curr_outgoing=NULL;
		word[i]->count_outgoing=0;
		word[i]->has_seen=0;
		word[i]->is_checked=0;
		word[i]->total_weight=0;
	}
}



/*********************************************************************************
    Function : read_unigram_data()
    Arguments: command line argument
    Return Value:None
    
    Function Description:
    This function counts the number of lines in unigram file. Calls initialize_word
    to initialize node array object.Then stores the unigrams in structure variable. 
**********************************************************************************/

void read_unigram_data(char* path)
{
	int i,j,k=0,sp;
	FILE *fpuni;
	char* ch;
	char* fname;
	char files[100];

	fname="/dvd1/data/1gms/vocab";

	sp=sprintf(files,"%s%s",path, fname);


	if((fpuni=fopen(files,"r"))==NULL)
                printf("\nError opening file.\n");

       	else
        {
		ch=(char *)calloc(sizeof(char),50);

		while(fgets(ch,50,fpuni)!=NULL)
		{	
			uSize++;
		}

		initialize_word(uSize);
		rewind(fpuni);

              for(i=0;i<uSize;i++)
              {
                	fscanf(fpuni,"%s",ch);
                	word[i]->token=(char *)malloc(sizeof(char)*strlen(ch));
                	strcpy(word[i]->token,ch);
			build_hash_arrays(word[i]->token, i);
			word[i]->index=i;                       
			fscanf(fpuni,"%d",&j);
		}
	}
	free(ch);
	fclose(fpuni);
}

/*********************************************************************************
    Function : add_incoming
    Arguments: "node" object, weight of the edge, index of incoming words
    Return Value:None
    
    Function Description:
    This function handles the incoming linked list implementation by adding edge 
    information.
**********************************************************************************/

void add_incoming(node* wd, int frequency,int index)
{
	edge* temp=(edge *)malloc(sizeof(edge));
	temp->freq=frequency;
	temp->index=index;
	temp->next=NULL;

	if(wd->incoming==NULL)
	{
		wd->incoming=temp;
	}
	else
	{
		wd->curr_incoming->next=temp;
	}
	wd->curr_incoming=temp;
}



/*********************************************************************************
    Function : add_outgoing
    Arguments: "node" object, weight of the edge, index of outgoing words
    Return Value:None
    
    Function Description:
    This function handles the outgoing linked list implementation by adding edge 
    information.
**********************************************************************************/

void add_outgoing(node* wd, int frequency,int index)
{
	edge* temp=(edge *)malloc(sizeof(edge));
	temp->freq=frequency;
	temp->index=index;
	temp->next=NULL;

	if(wd->outgoing==NULL)
	{
		wd->outgoing=temp;
	}
	else
	{
		wd->curr_outgoing->next=temp;
	}
	wd->curr_outgoing=temp;
}

/*********************************************************************************
    Function : binarySearch
    Arguments: character to be searched.
    Return Value:character's index in unigram array
    
    Function Description:
    This function performs binary search algorithm to find index in unigram array. 
**********************************************************************************/

int binarySearch(char* ch)
{
	int low,mid,high;
	double middle;
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
		
}

