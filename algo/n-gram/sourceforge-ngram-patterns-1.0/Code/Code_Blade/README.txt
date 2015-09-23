		N-gram-patterns	Team
		--------------------

Minimum System Requirements
---------------------------
System: IBM BladeCenter
Memory Requirements: pmem = 7Gb
Number of Nodes: 4
Processors Per Node(ppn) = 1

Important Note:
---------------
IBM BladeCenter might be suitable for some target tokens, usually high unigram cut-offs and 
very low associative cut-off. If you want to run with no unigram and associative cut-offs 
please use the google_patterns_altix code.

One such setting that works is given below. This is not the minimum setting as it is hard
to predict one. But this setting works for the specified 'target' file.
   
Unigram Cutoff = 100000
Associative Cutoff = 0.000000009 

How To Run
----------
./runit.sh google-ngram-data-path Unigram-Cutoff Associative-Cutoff target-file-path

For Eg:
./runit.sh /scratch1/pedersen/google-ngram 100000 0.000000009 target

Regards,
N-gram-patterns Team



