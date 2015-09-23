#!/bin/bash -l

module unload intelmpi
module load pathmpi
mpicc -o ngram network.c print_path.c graph_analysis.c analyze_v.c prune.c main.c  

echo "
    N-gram-patterns Copyright (C) 2007 Flamengo team
    This program comes with ABSOLUTELY NO WARRANTY;
    This is free software, and you are welcome to redistribute it
    under certain conditions;
"
qsub -v ROOT=$1,UCUTOFF=$2,ACUTOFF=$3,IFILE=$4 myscript -q bc -N Flamengo 
