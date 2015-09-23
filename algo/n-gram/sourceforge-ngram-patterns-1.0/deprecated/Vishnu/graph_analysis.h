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





#include<mpi.h>
#include<stdio.h>
#include<stdlib.h>
#include<math.h>
#include "analyze_v.h"

#ifndef GRAPH_ANALYSIS_H
#define GRAPH_ANALYSIS_H




// Not in use.
void graphInit(int _master , int _id);

// This method is called by every processor. Processor 0 is assigned as a master and the rest of processors are slaves.
void networkAnalysis(int id , node** ptrword, int arraySize, int np);

// Not in use.
void generateOutput(int id, node** ptrword , int arraySize , int np);

#endif
