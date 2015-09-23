#!/bin/bash -l
#PBS -l walltime=1:00:00,mem=4gb,nodes=1:ppn=4
#PBS -m abe

cd $PBS_O_WORKDIR
module load pathmpi


mpirun -np 1 -hostfile $PBS_NODEFILE $ROOT ./a.out > output

#
# ==== end of the sample script file ====

