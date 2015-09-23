#include "network.h"
#include <limits.h>
#ifndef INDEX_H
#define INDEX_H

typedef struct range
{
	int start;
	int end;	
}range;

typedef struct dictionary
{
	int base;
	int size;
	int num_word;
	range** content;
	node** words;
}dictionary;

range* new_range(int _start, int _end);
dictionary* new_dictionary(int _base, int _size, int _num_word, node** node_pool);
void build_dictionary(dictionary* _dict);
int get_range(char* _ch, dictionary* _dict, int* start, int* end);
void get_word_info(int* min, int* max, int* length, int size, node** words);

#endif
