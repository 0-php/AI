/***********************************************************************************
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
	Co-occurrence network creation Header File
	Programer Name: Darshan Paranjape
**********************************************************************************/
#ifndef NETWORK_H
#define NETWORK_H

#include<stdio.h>
#include <math.h>
#include<stdlib.h>
#include <string.h>


typedef struct edge
{
	int index;		//location in unigram array
	long int freq;		//weight
	struct edge *next;  	//Pointer to next element in the linked list
	int marked;		//variable used in path finding
	int marked_before;	//Variable used in path finding
}edge;

typedef struct node//Actual array of token(words from unigram) structures
{
	char* token;			//Unigram(single word)
	long int freq;		//Frequency of the Unigram
	edge *incoming;			//Head pointer of incoming linked list
	edge *outgoing;			//Head pointer of outgoing linked list
	edge *curr_incoming;		//Pointer to the last element of incoming linked list
	edge *curr_outgoing;		//Pointer to the last element of outgoing linked list
	int has_seen;			//Variable used in finding distinct networks
	int is_checked;			//Variable used in finding distinct networks
	int index;			//location in unigram array
	int count_outgoing; 		//Total number of outgoing edges.can be used to count total number of edges in the network.
	int count_incoming; 		//Total number of incoming edges
	long int total_out_weight; 	//Sum of all outgoing weights
	int incoming_nodes_added;	//Variable used in the beta stage
	int outgoing_nodes_added;	//Variable used in the beta stage
}node;


//Function to initialize every word structure
node** initialize_word(node** word,int uSize); 

//Function to read unigrams and store them in node structure
node** read_unigram_data(char* path, int n,node** word); 

//Function to store incoming and outgoing edge information in linked list.
void add_incoming(node* wd, long int frequency,int index);
void add_outgoing(node* wd, long int frequency,int index);

//Function to number of unigrams satisfying Unigram cutoff limit 
int count_unigram_size(char* path,int n);

//Function to count number of entries(lines) in Bi-gram files
int count_bigram_size(char* files);

//Function to implement binary search algorithm
int binarySearch(char* ch,int uSize, node** word);

//Function to build co-occurrence network using all 32 Bi-gram data files.
node** build_network(char* path,int n,node** word,int id,int p);

//Function to print Network information
void print_info();

//Function that displays Node information. Used for testing purpose
void display(node** word,int uSize);

#endif




