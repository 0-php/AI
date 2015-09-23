#include<string.h>
#include "prune.h"
//int getUnigramSize(char* path , long int);
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

   Author: Vishnu Praveen Pedireddi.
   Project: N-Gram Patterns.

  function:      getUnigramSize

  Input:         
  1.char*        The charecter string represents the path to the directory where the google n-gram data is present.
  2.long int     The value of long int represents the cut-off value for the unigram frequency.

  Output:
  1. int         The number of unigram occurences that are above the cut-off value.

  Purpose:       returns the number of words in UTF-8 format that are greater than cutoff limit.
                 This number represents the size of memory to be allocated. 
  Explanation:   The pruning of the data by specifing the minimum cutoff for a set of unigram data directly implies that we are not interested in 
                 any unigrams that are below certain cut-off limit. It logically follows that there is no need for us as program writers to allocate memory                   for such a unigram in the first place. To serve this purpose, this method returns the number of unigrams that pass the cutoff frequency 
                 test. 
*/
int getUnigramSize(char* path , long int cutoff)
{
  FILE *vocabFile = NULL;                                             // File poiner to open the vocab file
  char* vocabFilePath;                                                // A charecter string that holds the path to the unigram vocab file
  char filePath[60];                                                  // Absolute path location of the unigram file.
  int status , count = 0;                                // Variables used in programs.
  long int frequency;                                                 // The frequency of occurence of the unigram words.
  char dummy[60];                                                     // The token value of the unigram word.

  vocabFilePath = "/dvd1/data/1gms/vocab";                            // Location of the unigram file from the parent directory.
  status = sprintf(filePath,"%s%s", path , vocabFilePath);            // Get the absolute location of the unigram file.
  
  vocabFile = fopen(filePath,"r");                                    // Open the unigram file.
  
  if(vocabFile == NULL)                                               // If the file cannot be opened, then display the message.
    printf(" The file has not been opened "); 
  else
    {
        while(!feof(vocabFile))                                        // Untill the end of file is encountered, read the file.
         {
             fscanf(vocabFile , "%s", dummy);                         // read the token value.
	     if(feof(vocabFile))                                      // If end of file encountered, then quit finding for the next word.
                  break;

             fscanf(vocabFile , "%ld", &frequency);                   // read the frequency of the token.
	     if(feof(vocabFile))                                      // If end of file encounterd , then quit finding for the next word.
                  break;
             if(frequency >= cutoff)                                  // If the frequency is greater than cut-off then increment the unigram count.
                 count++;
                
         }
    }

//  printf("The value of count is  %d ", count);                        // word but returns 1 after it realizes it has no word to read. Therefore the while
                                                                      // loop above is entered one extra time.

  return count;                                                 // Return the unigram count.
}

//This method checks if the frequecy of the unigram word is greater than certain cut-off limit.
//If it is then, the word is included in the network. Thus, this method is the basis on which it is decided, 
// if the unigram should be in the network or not.
int checkWord(long int frequency , long int cutoff)
{
  if(frequency >= cutoff)
     return 1;
  else 
     return 0;
}

// This method checks the value of index used to derefernce into the word array , a valid index.
// This value of the index could be -1 if the word is not presend in the array of unigram words.
int notValidIndex(int index , int cutoffSize)
{

  if(index == -1)
     {
         return 1;
     }  
  if(index >= cutoffSize || index < 0)
     {
         printf(" The binary search has a bug in it.\n");
         printf(" This message is generated if the processor is trying to access a index in the array word whoose index is out of bounds.\n");
         return 1;
     }
  return 0;

}
// This function is used to determine if the bigram is to included in the path being displayed.
// Please note that this number is used if the bigram needs to included in the path. But should be present in the network built. 
// Because this effects the statistics of the network.
// Caution: This method is deprecated.
// Reason : This method is deprecated because if the value of frequency1 and frequency2 are a long integers, then thier multiplication will result in 
//          a value that is extremely high. For this reason, the method associativeRatio is defined.
int associativeCutNotSatisfied(long int frequency1 , long int frequency2 , long int biFrequency ,  double associativeCut)
{
   double ratio;
   ratio = (double)(biFrequency)/ (double)(frequency1*frequency2);
   if(ratio < associativeCut)
      return 1;
  
   return 0;
}

// This method returns the associative ratio. This ratio is used in the method used to print the path lenghts. 
double associativeRatio(long int frequency1 , long int frequency2 , long int biFrequency ,  double associativeCut)
{
   double ratio;
   ratio = ((double)(biFrequency)/(double)(frequency1))/(double)(frequency2);
   //printf("\nRatio %f\n", ratio);
   return ratio;
}



