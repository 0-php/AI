#!/bin/bash -l
module load pathscale
mpicc network.c hash.c
qsub -v ROOT=$1 -q bc script.sh
#
# ==== end of the sample script file ====

