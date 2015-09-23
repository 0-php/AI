************************README**********************
 
This is the alternate implementation of network analysis. 
This code returns the number of disjoint networks in the bigram files.

Please use the following command to run the system.
./runit	(path to current directory) (cutoff) (association cut) (input)

For example to run the code in my directory I use the following.

./runit /home/bc1/cs862125/google-ngram-patterns 100 0.23 input

please type in the the directory in which the code is present to run the test data.

The code is functional with test data.
The test data is present in the present directory.
To run the code with google data, set the value of a to be less than 32.
thus, the for loop is the following,
for(a= 0 ; a< 32 ; a++)


