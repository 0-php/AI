#include<stdio.h>
#include <math.h>
#include<stdlib.h>
#include <string.h>

#ifndef NETWORK_H
#define NETWORK_H


#define UNIGRAM_SIZE 13588391  //size of unigram array

int id;			/* Process ID */
int p;				/* Total number of processes */


typedef struct edge
{
	int index;//location in unigram array
	int freq;//weight
	struct edge *next;
}edge;

typedef struct node//Actual array of token(words from unigram) structures
{
	char* token;
	edge *incoming;//Starting pointer of incoming linked list
	edge *curr_incoming;//Current pointer of incoming linked list
	edge *outgoing;//Starting pointer of outgoing linked list
	edge *curr_outgoing;//Current pointer of outgoing linked list
	int has_seen;
	int is_checked;
	int index;//location in unigram array
	int count_outgoing;//Total number of outgoing edges.can be used to count total number of edges in the network.
	long int total_weight;
}node;

node** word;

void initialize_word(int size); //Function to initialize each one of the 13 million word structure

void add_incoming(node* wd, int frequency,int index);
void add_outgoing(node* wd, int frequency,int index);

void read_unigram_data(char* path);
void display();

#endif
