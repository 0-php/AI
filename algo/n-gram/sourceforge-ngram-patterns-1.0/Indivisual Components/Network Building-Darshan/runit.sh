#!/bin/bash -l

module unload intelmpi
module load pathmpi

mpicc -o oput.exe main.c network.c 

qsub -v ROOT=$1 myscript -q devel -N nwork