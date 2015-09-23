/* Copyright (C) 2007 Flamengo team.
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

   File: analyze.c
   Author: Bin Lan <lanxx019@d.umn.edu>
   Professor: Dr. Ted Pedersen

   This file contains the source code for all functions that used for network
   analysis.
   
   ALGORITHM:
   
   1. All the processor will do a local network analysis with its own partial
   network information.every token in the unigram data array has two checking 
   bit. is_checked and has_seen. They are initialized to be 0. We will go 
   through all the array to check each token to make sure that: if a token has 
   been seen before, then we go in the incoming and outgoing linked list to mark 
   all connected token's has_seen bit with the same network id, after this, we 
   mark the current token as is_checked with a network id. In this case, we make
   sure that we not only see every token but also all the tokens that connect to
   it, so that we cover every nodes in the network. Also, the id number can 
   automatically decide how many networks are there and which network this token 
   is belonged to.
   
   2. After the first step, every processor should have a network id array that
   contains its own network information. Then we will use a customized reduce
   function to compare all the network id arraies from all processors and 
   produce a global id array that has the network information of all nodes and
   edges, which in some sense gather all the connectivities from all processors.
   
   3. After we get the global network id, we will ask each processor collect its
   own network information, including total weight, number of nodes and edges 
   in each connected network as well as the total weight and total nodes and 
   edges for the whole network.
   
   BENCHMARK:
   
   walltime = 3:00:00, pmem = 3gb, ppn = 2
   nodes: varies.
   
   CPU:		File:		time: (seconds)
    2           test            0.000538
    4           test            0.000620 
    8           test            0.000651
   16           test            0.015461
   
    8           google          5140.201907
   16           google          5969.727353
   
   test: a sample test data file we made that has 41 nodes and 51 edges. 
         Included 5 networks and 1 disconnected node.
         
   google: the google n-gram data. We only use 1gram and 2gram files.      
   
   TODO:
   
   1. The reduce function is taking too much time. Need to modify it.
   2. Check if bigram contains self connected node.
   3. See if we really need both is_checked and has_seen.
   4. Can we use OpenMP somehow?
   5. Still some bugs here, find them.
*/

#include "analyze.h"

/* A constructor for id_list structure. Return a pointer.
*/
id_list* new_id_list(int _id, int _a_id)
{
	/* Allocate memory for new id_list */
	id_list* _id_list = (id_list*)malloc(sizeof(id_list));
	_id_list->id = _id;
	_id_list->array = _a_id;
	
	/* The next id_list is now NULL */
	_id_list->next = NULL;
	return _id_list;
}

/* MPI function to parallelly collect the network information.
*/
void gather_network_info(node** node_pool, int size, int id, int p)
{
	int i;
	int num_network;	/* Number of networks */
	int* local;		/* Local network id array */
	int* global;		/* Global network id array */
	int* g_weight;		/* Global total weight */
	int* g_num_edge;	/* Global numbers of edges */
	int* l_weight;		/* Local total weight */
	int* l_num_node;	/* Local numbers of nodes */
	int* l_num_edge;	/* Local numbers of edges */
	MPI_Op combine;		/* The name of our reduce operator */
	
	/* Allocate memory for both local and global network id array */
	local = (int*)malloc(size * sizeof(int));
	global = (int*)malloc(size * sizeof(int));
	
	/* First step: each processor does its own local analysis */
	analyze_network(node_pool, size);
	
	/* Create the operator */
	MPI_Op_create((MPI_User_function*)op_combine, 1, &combine);

	/* Copy the network id from unigram array to a int array */
	for (i = 0; i < size; i++)
	{
		/* If it is not connected at all, assign 0 */
		if (node_pool[i]->incoming == NULL && node_pool[i]->outgoing == NULL)
			local[i] = 0;
		/* Else assign the network id */
		else
			local[i] = node_pool[i]->is_checked;
	}

	/* Reduce */
	//MPI_Barrier(MPI_COMM_WORLD);
	MPI_Allreduce(local, global, size, MPI_INT, combine, MPI_COMM_WORLD);
	MPI_Barrier(MPI_COMM_WORLD);

	/* Sort the final result so we have a nicer looking array */
	num_network = sort_id(global, size);
	
	if (id == 0)
	{
		/*for (i = 0; i < size; i++)
		{
			printf("%d\n", global[i]);
		}*/
		printf("We have %d disjoint networks\n", num_network);
	}
	
	/* Now, we collect edge and weight information from other processor*/
	//MPI_Barrier(MPI_COMM_WORLD);
	for (i = 0; i < size; i++)
	{
		/* Assign the new id to the unigram array */
		node_pool[i]->has_seen = global[i];
		node_pool[i]->is_checked = global[i];
	}

	/* Allocate memory for the arraies
	   We do not need a global node number because now every processor can
	   easily decide the number of nodes of each networks.
	*/
	g_weight = (int*)malloc(sizeof(int) * (num_network + 1));
	g_num_edge = (int*)malloc(sizeof(int) * (num_network + 1));
	l_weight = (int*)malloc(sizeof(int) * (num_network + 1));
	l_num_node = (int*)malloc(sizeof(int) * (num_network + 1));
	l_num_edge = (int*)malloc(sizeof(int) * (num_network + 1));

	/* Initialize the arraies. Sometimes they are not 0. */
	for (i = 0; i < num_network + 1; i++)
	{
		g_weight[i] = 0;
		g_num_edge[i] = 0;
		l_weight[i] = 0;
		l_num_node[i] = 0;
		l_num_edge[i] = 0;
	}
	
	/* Now, we collect information */
	for (i = 0; i < size; i++)
	{
		/* The first element contains the info for whole network */
		l_weight[0] += node_pool[i]->total_out_weight;
		l_num_node[0]++;
		l_num_edge[0] += node_pool[i]->count_outgoing;
		
		/* For individual connected network */
		if (node_pool[i]->is_checked != 0)
		{
			l_weight[node_pool[i]->is_checked] += node_pool[i]->total_out_weight;
			l_num_node[node_pool[i]->is_checked]++;
			l_num_edge[node_pool[i]->is_checked] += node_pool[i]->count_outgoing;
		}
	}

	/* Now we reduce the local edge and weight result */
	//MPI_Barrier(MPI_COMM_WORLD);
	MPI_Reduce(l_weight, g_weight, num_network + 1, MPI_INT, MPI_SUM, 0, MPI_COMM_WORLD);
	MPI_Reduce(l_num_edge, g_num_edge, num_network + 1, MPI_INT, MPI_SUM, 0, MPI_COMM_WORLD);
	//MPI_Barrier(MPI_COMM_WORLD);

	/* Processor 0 will print the network info */
	if (id == 0)
	{
		printf("***********************Network info*****************************\n");
		for (i = 0; i <= num_network; i++)
		{
			if (i == 0)
				printf("The whole network:\n");
			else
				printf("\n\nNetwork %d\n", i);
			printf("Num of nodes: %d\n", l_num_node[i]);
			printf("Num of edges: %d\n", g_num_edge[i]);
			printf("Total edge weight: %d\n", g_weight[i]);
		}
	}
	
	/* Free the memory */
	free(l_weight);
	free(l_num_node);
	free(l_num_edge);
	free(g_weight);
	free(g_num_edge);
	MPI_Op_free(&combine);
}

/* Parallel local function for analysis network.
*/
int analyze_network(node** node_pool, int size)
{
	int network_id;		/* The current network id */
	int last_unseen;	/* Last node that has no network id */
	int i;
	
	network_id = 0;
	last_unseen = 0;
	
	/* If there is any node that has no network id, we continue */
	while ((i = has_checked_all(node_pool, size, last_unseen)) != -1)
	{
		network_id++; /* Increase id for a new network */
		last_unseen = i + 1; /* The new start index for searching */
		/* Check all childern of this node */
		check_all_branch(node_pool[i], node_pool, network_id);
	}
	return network_id;
}

/* Check all the children of a node.
*/
void check_all_branch(node* _node, node** node_pool, int id)
{
	/* If has_seen is same as id, then we already checked this node before */
	if (_node->has_seen != id)
	{
		_node->has_seen = id;
		edge* temp;
		
		/* Check all incoming nodes */
		temp = _node->incoming;
		while (temp != NULL)
		{
			if (node_pool[temp->index]->is_checked == 0)
				check_all_branch(node_pool[temp->index], node_pool, id);
			temp = temp->next;
		}
		
		/* Check all outgoing nodes */
		temp = _node->outgoing;
		while (temp != NULL)
		{
			if (node_pool[temp->index]->is_checked == 0)
				check_all_branch(node_pool[temp->index], node_pool, id);
			temp = temp->next;
		}
		
		/* The node right is checked */
		_node->is_checked = id;
	}
}

/* Is there any node that we have not checked yet?
*/
int has_checked_all(node** node_pool, int size, int start_index)
{
	int i;
	
	/* Start from the last unseen index to save us some time */
	for (i = start_index; i < size; i++)
	{
		/* Yes */
		if (node_pool[i]->has_seen == 0)
			return i;
	}
	/* No */
	return -1;
}

/* MPI reduce operator
*/
void op_combine(int* input, int* result, int* length, MPI_Datatype* dtype)
{
	int min_i, min_r, min, i, j, new_i, new_r, count, id;
	id_list** list;
	
	/* We first sort both input and output and get the number of networks 
	   from each array
	*/
	min_i = sort_id(input, *length);
	min_r = sort_id(result, *length);
	
	/* If network A a has n disconnected networks, and network B has m 
	   disconnected networks, then after we combine them, the worest case is
	   that the new network has m + n disconnected network.
	*/
	min = min_r + min_i;
	
	/* Allocate the memory for id_list, all element should be NULL now */
	list = (id_list**)malloc(sizeof(id_list*) * min);
	for (i = 0; i < min; i++)
		list[i] = NULL;
			
	//MPI_Comm_rank(MPI_COMM_WORLD, &id);
	//printf("Processor %d start reduce\n", id);
	
	/* Now we merge two network id arraies */
	count = 1; /* 0 are the nodes that totally disconnected */
	for (i = 0; i < *length; i++)
	{
		/* If one of them is in a connected network */
		if (input[i] != 0 || result[i] != 0)
		{
			/* Check if their id's are in the id_list */
			new_i = -1;
			new_r = -1;
			for (j = 0; j < min; j++)
			{
				if (input[i] != 0 && is_in_the_list(input[i], list[j], 0) == 1)
					new_i = j;
				if (result[i] != 0 && is_in_the_list(result[i], list[j], 1) == 1)
					new_r = j;
			}
			
			/* If both are connected and both id's are not in the 
			   list, then we add two new entry in the id_list.
			*/
			if (new_i == -1 && new_r == -1 && input[i] != 0 && result[i] != 0)
			{
				id_list* a_list = new_id_list(input[i], 0);
				id_list* b_list = new_id_list(result[i], 1);
				list[count] = a_list;
				a_list->next = b_list;
				count++;
			}
			/* If one of them is disconnected, then we only need to 
			   add one new entry.
			*/
			else if (new_i == -1 && new_r == -1 && input[i] != 0 && result[i] == 0)
			{
				id_list* a_list = new_id_list(input[i], 0);
				list[count] = a_list;
				count++;
			}
			else if (new_i == -1 && new_r == -1 && input[i] == 0 && result[i] != 0)
			{
				id_list* a_list = new_id_list(result[i], 1);
				list[count] = a_list;
				count++;
			}
			
			/* If both are connected and one of them is not in the 
			   id_list, then we add one new entry follow the one 
			   that is already in the id_list.
			*/
			else if (new_i == -1 && new_r != -1 && input[i] != 0 && result[i] != 0)
			{
				id_list* temp;
				id_list* a_list = new_id_list(input[i], 0);
				temp = list[new_r];
				while (temp->next != NULL)
					temp = temp->next;
				temp->next = a_list;
			}
			else if(new_r == -1 && new_i != -1 && result[i] != 0 && input[i] != 0)
			{
				id_list* temp;
				id_list* a_list = new_id_list(result[i], 1);
				temp = list[new_i];
				while (temp->next != NULL)
					temp = temp->next;
				temp->next = a_list;
			}
			
			/* If both are connected and both id's are in the 
			   id_list, then we need to merge both entry (the whole 
			   list) together.
			*/
			else if (new_r != -1 && new_i != -1 && new_r != new_i)
			{
				/* We would like to merge them into the smaller
				   entry.
				*/
				if (new_r < new_i)
				{
					id_list* temp = list[new_r];
					while (temp->next != NULL)
						temp = temp->next;
					temp->next = list[new_i];
					list[new_i] = NULL;
				}	
				else
				{
					id_list* temp = list[new_i];
					while (temp->next != NULL)
						temp = temp->next;
					temp->next = list[new_r];
					list[new_r] = NULL;
				}
			}
		}
	}
	
	/* Now we have a id_list that has the new connectivity information, we
	   need to update it to the network id array. 
	*/
	for (i = 0; i < *length; i++)
	{
		for (j = 0; j <= count; j++)
		{
			/* If the result is connected */
			if (result[i] != 0 && is_in_the_list(result[i], list[j], 1))
			{
				result[i] = j;
				break;
			}
			/* Else if the input is connected */
			else if (input[i] != 0 && is_in_the_list(input[i], list[j], 0))
			{
				result[i] = j;
				break;
			}
		}
	}
	
	free(list);
}

/* Is a network id already in the id_list?
*/
int is_in_the_list(int id, id_list* _list, int array_id)
{
	id_list* temp;
	temp = _list;
	
	/* While we are not the last one, we keep checking */
	while (temp != NULL)
	{
		if (temp->id == id && temp->array == array_id)
			return 1;
		else
			temp = temp->next;
	}
	return 0;
}

/* Sort the network id array, so it is in order. After op_combine, the network 
   id array is not sorted. Namely, we might have network 1, 2, 5 and 7. But we 
   do not have networ 3, 4, and 6. After the function, we should have network 1,
   2, 3, and 4 instead.
*/
int sort_id(int* array, int size)
{
	int count, i, j, id;
	count = 0;
	for (i = 0; i < size; i++)
	{
		/* All id we used so far should be smaller than count */
		if (array[i] > count)
		{
			/* So this is a new network */
			count++;
			id = array[i];
			
			/* Check if there is other nodes in the same network and 
			   update their id as well
			*/
			for (j = i; j < size; j++)
			{
				if (array[j] == id)
					array[j] = count;
				else if(array[j] == count)
					array[j] = INT_MAX - id;
			}
		}	
	}
	return count;
}
