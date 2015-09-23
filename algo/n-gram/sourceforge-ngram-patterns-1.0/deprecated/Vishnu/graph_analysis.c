#include "graph_analysis.h"
/*
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

   Author: Vishnu Praveen Pedireddi.
   

*/




// Not in use..
void graphInit(int _master ,int _id)
{




}
// This method determines which processor is calling the method and guides it accordingly.
// If master calls it, it calls masterAnalyzeNetwork.
// If slaves calls it, it calls the slaveAnalyzeNetwork.
void networkAnalysis(int id, node**ptrword ,int  arraySize , int np)
{

  if(id==0)
    {
      //printf(" From master ");
      masterAnalyzeNetwork(ptrword , arraySize , id , np);
    }
  else
    {

      //printf(" From Slave ");
      slaveAnalyzeNetwork(ptrword , arraySize , id , np);
     }


}
 
// Not in use..
void generateOutput(int id, node** ptrword , int arraySize , int np)
{
  int counter;
  for(counter = 0; counter < arraySize ; counter++)
    printf(" The ids are: %d for word %s", ptrword[counter]->has_seen, ptrword[counter]->token);
  
 
}   
