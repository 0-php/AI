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
	Google N-Gram Project
	Programer Name: Darshan Paranjape
**********************************************************************************/


/************************************************************************************
PROBLEM STATEMENT:

To build and use a co-occurrence network from the Google N-gram Data. Words that occur 
together in a Google 2-gram should be linked together, assuming that each of the word 
occurs individually 200 or more times. Approach to this problem should be parallel, 
program should run faster by increasing numbers of processors. 
************************************************************************************/

/* System include file */
#include <stdio.h>
#include <math.h>
#include <stdlib.h>
#include <string.h>
#include <mpi.h>

/* Project include file */
#include "network.h"


int main(int argc,char *argv[])
{
	
	/* Variable Declaration */
	int	n=0;	  	/* Unigram Cutoff limit. We're going to take this from command line */
	node**	word; 		/* Pointer to array of node structures.*/
	double	elapsed_time;	/* Benchmarking */
	int	id;		/* Process ID */
	int	p;		/* Total number of processes */
	int 	uSize; 		/* Size of Unigram Array */

	int i;

	int l_num_edge = 0;
	int g_num_edge = 0;


	/* MPI Initialization */
	MPI_Init(&argc , &argv);

	/* Getting process id/Rank and total number of processes */
	MPI_Comm_rank (MPI_COMM_WORLD, &id);
	MPI_Comm_size (MPI_COMM_WORLD, &p);

	/* Barrier Synchronization */
	MPI_Barrier (MPI_COMM_WORLD);
	elapsed_time= -MPI_Wtime();

	/* Gives the number of unigrams. This will be the size of array of node structures. */
	uSize = count_unigram_size(argv[1],n);	   

	/* Build the whole Co-occurance network on all 32 files. */
	word = build_network(argv[1],n,word,id,p); 

	/* Calculating number of edges locally */
	for (i = 0; i < uSize; i++)
	{
		l_num_edge += word[i]->count_outgoing;
	}

	MPI_Barrier (MPI_COMM_WORLD);

	/* Combining the number of edges from all processors in process 0. */
	MPI_Reduce(&l_num_edge, &g_num_edge, 1, MPI_INT, MPI_SUM, 0, MPI_COMM_WORLD);

	MPI_Barrier(MPI_COMM_WORLD);
	
	if(id==0)
	{
		elapsed_time += MPI_Wtime();
		printf("\n\n\nTotal number of edges is= %d\n", g_num_edge);
		printf("\n\nTime to build network= %lf Seconds",elapsed_time);
		printf("\nNumber of processors: %d\n", p);
		fflush(stdout);
	}	
			
	MPI_Finalize();
	return 0;
}
