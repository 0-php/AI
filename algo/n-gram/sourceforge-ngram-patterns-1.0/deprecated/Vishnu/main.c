/*********************************************************************************
	CS8621- Advanced Computer Architecture
	Google N-Gram Project
	Programer Name: Darshan Paranjape
**********************************************************************************/


/************************************************************************************
PROBLEM STATEMENT:

To build and use a co-occurrence network from the Google N-gram Data. In alpha stage, 
words that occur together in a Google 2-gram should be linked together, assuming that 
each of the word occurs individually 200 or more times. Approach to this problem should 
be parallel, program should run faster by increasing numbers of processors. 
************************************************************************************/

/* System include file */
#include <stdio.h>
#include <math.h>
#include <stdlib.h>
#include <string.h>
#include <mpi.h>

/* Project include file */
#include "network.h"
//#include "analyze.h"
//#include "graph_analysis.h"
#include "analyze_v.h"
#include "print_path.h"
#include "prune.h"

int main(int argc,char *argv[])
{
	/* Code for building the network 
	 * Author: Darshan
	 */
	
	/* Variable Declaration */
	int	n=0;	  	    /* Unigram Cutoff limit. We're going to take this from command line */
	node**	word; 		    /* Pointer to array of node structures.*/
	double	elapsed_time;	    /* Benchmarking */
	int	id;		    /* Process ID */
	int	p;		    /* Total number of processes */
	int 	uSize; 		    /* Size of Unigram Array */
        double  associativeCutOff;   /* Associative cut-off */
 
	/* MPI Initialization */
	MPI_Init(&argc , &argv);

	/* Getting process id/Rank and total number of processes */
	MPI_Comm_rank (MPI_COMM_WORLD, &id);
	MPI_Comm_size (MPI_COMM_WORLD, &p);

	/* Barrier Synchronization */
	MPI_Barrier (MPI_COMM_WORLD);
	elapsed_time= -MPI_Wtime();

	/* Gives the number of unigrams that satisfies cutoff condition. This will be the size of word array. */
	//uSize = count_unigram_size(argv[1],n);  This method was used by darshan before the cut-off was implemented. This is just for reference.
        n = atoi(argv[2]);
        uSize = getUnigramSize(argv[1], (long int) n);
	/* Build the whole Co-occurance network on all 32 files.
	 * Only the unigrams that satisfy cutoff condition are considered while building the network.
	 */
        //n = atoi(argv[2]);
        associativeCutOff = atof(argv[3]);

	word = build_network(argv[1],n,word,id,p,associativeCutOff); 
 
	MPI_Barrier (MPI_COMM_WORLD);

	if(id==0)
	{
		elapsed_time += MPI_Wtime();
		printf("\n\nTime to build network= %lf Seconds\n",elapsed_time);
		printf("\n\nNumber of processors: %d\n", p);
		fflush(stdout);
	}

	/* End of building network */
	
	/* Code for analysis the network layout
	 * Author: Bin  
	 */
	MPI_Barrier(MPI_COMM_WORLD);
	elapsed_time = -MPI_Wtime();
	MPI_Barrier(MPI_COMM_WORLD);

	// Vishnu's method
	networkAnalysis(id, word, uSize, p);


	MPI_Barrier(MPI_COMM_WORLD);
	if (id == 0)
	{	
		elapsed_time += MPI_Wtime();
		printf("Used %lf seconds to analysis the network\n", elapsed_time);
	}

	// Anurag's method for Beta stage
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
		
	MPI_Finalize();
	return 0;
}
