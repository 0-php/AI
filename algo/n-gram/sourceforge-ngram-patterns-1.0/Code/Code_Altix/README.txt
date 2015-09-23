                N-gram-patterns Team
                ---------------------

Minimum System Requirements
---------------------------
System: SGI ALTIX 3700 BX2
Memory Requirements: mem = 50Gb
Number of Nodes: 1
Processors Per Node(ppn) = 4

Important Note:
---------------
SGI ALTIX 3700 BX2 is suitable for zero unigram cut-off and no association cut-off.

How To Run
----------
./runit.sh google-ngram-data-path Unigram-Cutoff Associative-Cutoff target-file-path

For Eg:
./runit.sh /scratch1/pedersen/google-ngram 0 0.0 target

Regards,
N-gram-patterns Team

