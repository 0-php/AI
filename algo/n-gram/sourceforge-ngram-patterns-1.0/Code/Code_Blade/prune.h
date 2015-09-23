// This method returns the number of unigrams above the cut-off.
#include <stdio.h>
#ifndef PRUNE_H
#define PRUNE_H
// This method determines the number of unigrams that are above the cut-off in the vocab file 
int getUnigramSize(char* , long int);
// This method checks if the frequency of the unigram is above the cut-off limit.
int checkWord(long int , long int);
// This method checks if the index to be looked into the array of nodes is valid or invalid.( Deprecated )
int notValidIndex(int , int);
// This method is used to return the associative ratio of the bigram found in the 2 gram file.
double associativeRatio(long int , long int , long int ,  double );

#endif
