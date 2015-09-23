/*********************************************************************************
	CS8621- Advanced Computer Architecture
	N-gram-patterns 
**********************************************************************************/


/************************************************************************************
PROBLEM STATEMENT:

To build and use a co-occurrence network from the Google N-gram Data. In alpha stage, 
words that occur together in a Google 2-gram should be linked together, assuming that 
each of the word occurs individually 200 or more times. Approach to this problem should 
be parallel, program should run faster by increasing numbers of processors. 
************************************************************************************/
/*******************************BENCHMARKING*******************************************
                              IBM Blade Center
                              ----------------
Input File: target
pmem: 7Gb
Unigram Cutoff = 100000
Associative Cutoff =  0.000000009 

target File
-----------
Nike 1
thrilling 2
wonderful 2
miserable 1
tragic 4
Bush 1
Duluth 4
duluth 4
abczzzddd 4

Number Of Processors                                            Time Taken To Print Query(Seconds)
        4                                                              271.789171 
        8                                                              217.694062 
 

***************************************************************************************/

/* System include file */
#include <stdio.h>
#include <math.h>
#include <stdlib.h>
#include <string.h>
#include <mpi.h>

/* Project include file */
#include "network.h"
#include "analyze.h"
#include "print_path.h"
#include "prune.h"

int main(int argc,char *argv[])
{
	/* Variable Declaration */
	int	n=0;	  	    /* Unigram Cutoff limit. We're going to take this from command line */
	node**	word; 		    /* Pointer to array of node structures.*/
	double	elapsed_time;	    /* Benchmarking */
	double  total_time;	    /* Benchmarking total time */
	int	id;		    /* Process ID */
	int	p;		    /* Total number of processes */
	int 	uSize; 		    /* Size of Unigram Array */
        double  associativeCutOff;   /* Associative cut-off */
 
	/* MPI Initialization */
	MPI_Init(&argc , &argv);

	/* Getting process id/Rank and total number of processes */
	MPI_Comm_rank (MPI_COMM_WORLD, &id);
	MPI_Comm_size (MPI_COMM_WORLD, &p);
       	
	// Barrier Synchronization
        MPI_Barrier (MPI_COMM_WORLD);
        total_time= -MPI_Wtime();

	/*********Captures Unigram Cut-Off and gives the number of unigrams that satisfies cutoff condition.*****/
	// Author: Vishnu Praveen Pedireddi
        n = atoi(argv[2]);
        uSize = getUnigramSize(argv[1], (long int) n);
	//***************************************Unigram Cut-Off captured*************************************************

	//*************************************Associative Cutoff*********************************************************
	// Author: Vishnu Praveen Pedireddi
        associativeCutOff = atof(argv[3]);
	//*******************************Associative Cutoff Captured******************************************************

	/*********************************************** Code for building the network************************************
         *Author: Darshan Paranjape
         */
        /* Build the whole Co-occurance network on all 32 files.
         * Only the unigrams that satisfy cutoff condition are considered while building the network.
         */
	
	/* Barrier Synchronization */
        MPI_Barrier (MPI_COMM_WORLD);
        elapsed_time= -MPI_Wtime();

	word = build_network(argv[1],n,word,id,p); 
 
	MPI_Barrier (MPI_COMM_WORLD);

	if(id==0)
	{
		elapsed_time += MPI_Wtime();
		printf("\n\nTime to build network= %lf Seconds\n",elapsed_time);
		printf("\n\nNumber of processors: %d\n", p);
		fflush(stdout);
	}
	
	/************************************************ End of building network*****************************************/
	
	

	/****************************************** Code for analysis the network layout************************************
	 * Author: Bin Lan 
	 */
        
	/* Barrier Synchronization */
	MPI_Barrier(MPI_COMM_WORLD);
	elapsed_time = -MPI_Wtime();
	MPI_Barrier(MPI_COMM_WORLD);

	// Bin's method
	gather_network_info(word, uSize, id, p);


	MPI_Barrier(MPI_COMM_WORLD);
	if (id == 0)
	{	
		elapsed_time += MPI_Wtime();
		printf("Used %lf seconds to analysis the network\n", elapsed_time);
	}
	
	//**************************************************Network analysis done********************************************

	

	/*****************************************************Printing Query************************************************
	 *Author: Anurag Jain
`	 */
        
	/* Barrier Synchronization */
	MPI_Barrier (MPI_COMM_WORLD);
	elapsed_time= -MPI_Wtime();
	
	//Print the paths for the tokens in the specified file
	print_query(word, argv[4], id, p, uSize, associativeCutOff);

	if (id == 0)
	{
		elapsed_time += MPI_Wtime();
		printf("\n\nTime to print query is = %lf Seconds\n",elapsed_time);
		fflush(stdout);
	}
		
	//********************************************************Printing done************************************************
	
	//*****************************************************Time taken by whole system**************************************
	if (id == 0)
        {
                total_time += MPI_Wtime();
                printf("\n\nTime taken by the whole system is = %lf Seconds\n",total_time);
                fflush(stdout);
        }
	//**********************************************************************************************************************
	MPI_Finalize();
	return 0;
}
