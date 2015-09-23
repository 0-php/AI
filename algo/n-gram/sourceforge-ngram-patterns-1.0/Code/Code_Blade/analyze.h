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

   File: analyze.h
   Author: Bin Lan <lanxx019@d.umn.edu>
   Professor: Dr. Ted Pedersen

   This file contains the functions used for analysis the network structures.   	
*/

#include <limits.h>
#include "network.h"
#include "mpi.h"
#ifndef ANALYZE_H
#define ANALYZE_H

/* The structure for holding temporary id info when we combine two network 
   id arraies.

   id: the unique network id.
   array: the array identify number.
   next: the pointer to next id list.
*/
typedef struct id_list
{
	int id;
	int array;
	struct id_list* next;
}id_list;

/* This function create a new id_list structure and return the pointer to the
   newly created structure.
   
   Input:
   _id: the network id.
   _a_id: the array identify number.
   
   Return:
   a new pointer to the id_list structure.
*/void gather_network_info(node** node_pool, int size, int id, int p);
id_list* new_id_list(int _id, int _a_id);

/* This function sequentially analysis a network's structure.

   Input:
   node_pool: unigram token array.
   size: size of the unigram data.
   
   Return:
   number of disconnected networks.                                                        
*/                               
int analyze_network(node** node_pool, int size);

/* This function checks all the directly connected children of a node. The node
   will be marked as is_checked with its network id.

   Input:
   _node: the node which is being checked.
   node_pool: the array of all the network nodes.
   id: the network id.
   
   Return:
   None.
*/   
void check_all_branch(node* _node, node** node_pool, int id);

/* This function checks if the there is any nodes in the array still has not 
   been seen by any other nodes.

   Input:
   node_pool: the array of all the nodes in the network.
   size: the number of the nodes.
   start_index: the index of the last node we check that has not been seen 
                before.
   
   Return:
   -1: if all elements has been assigned a network id.
   index: otherwise return the first element that has no network id.
*/   
int has_checked_all(node** node_pool, int size, int start_index);

/* This function parallely analyzsis a network and print out the network 
   information.

   Input:
   node_pool: the unigram array.
   size: size of the unigram array.
   id: processor id.
   p: number of processors.
   
   Return:
   None.
*/
void gather_network_info(node** node_pool, int size, int id, int p);

/* This function is a customized MPI reduce function for gathering network 
   information.

   Input:
   input: local network id array.
   result: global network id array.
   length: size of the network id array.
   dtype: MPI data type (shoule be always int).
   
   Return:
   None.
*/

void op_combine(int* input, int* result, int* length, MPI_Datatype* dtype);

/* This function checks if a network id appears in a set of id_list.

   Input:
   id: the network id we need to check.
   _list: the current id list.
   array_id: which array the id belongs to.
   
   Return:
   1: if id appears in the list.
   0: otherwise.
*/
int is_in_the_list(int id, id_list* _list, int array_id);

/* This function sort the network id array so all id's will be in order.

   Input:
   array: network id array we need to sort.
   size: size of the network id array.
   
   Return:
   Number of different network id's.
*/
int sort_id(int* array, int size);

#endif
