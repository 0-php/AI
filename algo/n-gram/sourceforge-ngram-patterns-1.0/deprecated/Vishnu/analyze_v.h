#include "network.h"
#ifndef ANALYZE_V_H
#define ANALYZE_V_H

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





// This method is called by Processor 0. The processor 0 is the master. All the other processor are the slaves.
// This method which is called by the master, regulates the behavior of the slaves.
void masterAnalyzeNetwork(node** , int , int , int);

// This method is called by all the processors except the master. Every slave processor will work as directed by the master.
// Every slave will retain control in the master as long as the master instructs it to.
void slaveAnalyzeNetwork(node** , int , int , int);

// This method checks for children of a given node. The children are marked by network id's.
// Since this is a DFS, all the children as accessed in DFS fashion.
// This method is called by master as well as slaves.
void checkAllBranch(node* , node**  , int , int , int , int);

// This method is called by every processor to traverse for occurence of newly discovered nodes. The newly discovered nodes 
// are ones which are discovered by the slave processors and which were not broadcasted by the master.
void checkAllBranch2(node* , node**  , int , int , int , int);

// 
int slavesNewNodeCheck(int );
         

// Broadcast the number from the master to every other slave.

void broadcast_Message(int , int , int);


void slavesBroadcast(int , node** , int);

void receiveFromSlave(node**);

// The method localCheck performs a localized check on every processor if any new nodes are discovered by the slaves.
int localCheck(node** , int, int ,int);

// This method is called by the master to check for any nodes that do not belong to a network that were being searched until now.
// This means it returns a start point for the next network.
int nextNode(node** , int , int);

// This variable is maintained in the slave that holds the current network that is being analyzed.
int slaveNetworkId;

// This variable determines if the network is complete.
int recurseAgain;

#endif
