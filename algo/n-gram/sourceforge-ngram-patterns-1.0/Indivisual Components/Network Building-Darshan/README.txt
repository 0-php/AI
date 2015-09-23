This folder contains the files & scripts to build the co-occurrence network on
all 32 bi-gram files. This is an indivisual component that just handles network
creation functionality. It does not run end to end system.

In end to end system, the network creation code will be called first. Once the network
gets built, it will be used by network analyasis and path finding function.
We might run the end to end system on altex. Network creation code has been throughly 
tested on MSI IBM Blade center system. All the benchmarking results are done on Blade 
center system. It has not been completely tested on other MSI systems. Also in end to 
end system, unigram cutoff code is integrated with network creation code.

For above reasons I'm providing network creation code without unigram cutoff as indivisual 
component.


 
The runit.sh in this folder should be run on MSI IBM Blade center system ONLY. 

Mininum system requirement for network creation: walltime=1:00:00,pmem=7gb,nodes=4:ppn=1	
							 running with 4 processors

Suggested system requirement for network creation: walltime=1:00:00,pmem=3gb,nodes=16:ppn=2
							   running with 32 processors


Files description:

runit.sh : runit.sh in this folder handles the indivisual component i.e. 
	    component to build the network. It takes as an argument, the path to
	    the google N-gram data. It creates output file 'output.txt' in the 
	    folder from where it runs. Output file displays total number of unigrams,
	    number of bigrams in each bigram file, total number of network edges
	    handled, time taken to create the network and number of processors used.

	    E.g.	 ./runit.sh /scratch1/cs862124

network.h: Network building header file.
	    This file ocntains all the data structures used while building the network
	    along with the list of functions in network.c
           
network.c: Network building code file.
	    This file contains all the function codes required to build the network.