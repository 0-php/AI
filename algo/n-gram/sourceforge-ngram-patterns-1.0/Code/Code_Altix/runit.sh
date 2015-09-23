#!/bin/bash -l

echo "
    N-gram-patterns Copyright (C) 2007 Flamengo team
    This program comes with ABSOLUTELY NO WARRANTY;
    This is free software, and you are welcome to redistribute it
    under certain conditions;
"
icc -o ngram network.c analyze.c print_path.c prune.c main.c -lmpi

qsub -v ROOT=$1,UCUTOFF=$2,ACUTOFF=$3,IFILE=$4 myscript -N Flamengo 
