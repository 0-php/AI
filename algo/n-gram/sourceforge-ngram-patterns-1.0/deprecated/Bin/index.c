/* We are trying to build a "Dictionary" here. The unigram data will
   be the words in the dictionary. And we will have another int array
   the stores the beginning and ending index of all the words starts
   with the same character. For fast access, the index info of a 
   certain character, say "a", will be stored in i'th block of the int 
   array, where i is the UTF-8 value of "a". 

   It is not being used anymore, simply because a simple binary search 
   would achieve our goal 
*/
#include "index.h"

range* new_range(int _start, int _end)
{
	range* _range;
	_range = (range*)malloc(sizeof(range));
	_range->start = _start;
	_range->end = _end;
	return _range;
}

dictionary* new_dictionary(int _base, int _size, int _num_word, node** node_pool)
{
	dictionary* _dict;
	_dict = (dictionary*)malloc(sizeof(dictionary));
	_dict->base = _base;
	_dict->size = _size;
	_dict->num_word = _num_word;
	_dict->content = (range**)malloc(_size * sizeof(range*));
	_dict->words = node_pool;
	return _dict;
}

void build_dictionary(dictionary* _dict)
{
	int i, index;
	for (i = 0; i < _dict->num_word; i++)
	{
		index = _dict->words[i]->token[0] - _dict->base;
		if (_dict->content[index] == NULL)
		{
			range* _range;
			_range = new_range(i, i);
			_dict->content[index] = _range;
		}
		else
		{
			_dict->content[index]->end++;
		}
	}
}

int get_range(char* _ch, dictionary* _dict, int* start, int* end)
{
	int index;
	index = _ch[0] - _dict->base;
	*start = _dict->content[index]->start;
	*end = _dict->content[index]->end;
}

void get_word_info(int* min, int* max, int* length, int size, node** words)
{
	int i;
	*min = INT_MAX;
	*max = 0;
	*length = 0;
	for (i = 0; i < size; i++)
	{
		*min = (*min < words[i]->token[0]) ? *min : words[i]->token[0];
		*max = (*max > words[i]->token[0]) ? *max : words[i]->token[0];
		*length = (*length > strlen(words[i]->token)) ? *length : strlen(words[i]->token);
	}
}
