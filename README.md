# SDRAP - Scrambled DNA Rearrangements Annotation Pipeline
***UNDER CONSTRUCTION***

Brief description of software


## Prerequisites
SDRAP was written on a **CentOS 6.7** machine with **PHP 5.3.3**, **MySQL 5.6.31** and **Apache 2.2.15**. Backwards compatibility cannot be guaranteed, so newer versions of these softwares may cause unexpected behaviors.


## Installation
1 clone or download and unzip this repository into the desired directory (must be inside the website root directory of your machine to be accessible remotely via internet browser)
2 open a terminal window and `cd` into the SDRAP directory
3 run the command `./install` (might require sudo rights)


# Usage
Simply open up SDRAP in a browser, enter required parameters, select desired genome sequence files and click the "Annotate" button. The execution of the pipeline may take a few minutes to several hours depending on the size of the input dataset; make sure your browser does not time out during the execution of the pipeline.


## Input
As input to the pipeline, the correct precursor and product sequence files must be selected (see Genome Sequence Files), required parameters must be specified (see Required Parameters), and if desired, optional parameters can be specified (see Optional Parameters).


###### Genome Sequence Files
To add a sequence file for sequences in the precursor, open a terminal window `cd` into the SDRAP directory in a terminal window and run:
```
./add-sequence-file.sh precursor <file>
```
To add a sequence file for sequences in the product, open a terminal windot, `cd` into the SDRAP directory and run:
```
./add-sequence-file.sh product <file>
```
The two commands may require sudo rights and any browser window with the SDRAP page loaded may need to be forcibly refreshed to see the newly added files in the list of options.

Sequence files are accepted in .fasta and .fna format. Delimiters for the selected input files can be specified. SDRAP will break the description line of each nucleotide sequence into fields based on the specified delimiter and only consider the first field for future reference to the sequence. Therefore, please be sure to include a unique identifier as the first field in the description line of each nucleotide sequence across both the precursor and product sequence files.


## Required Parameters


###### MySQL Database
**Hostname** - the host name, or IP address identifying the MySQL server which SDRAP should use. If using the MySQL server of the machine where SDRAP is located, leave this field as 'localhost'.

**Username** - the MySQL username that SDDRAP should use.

This user should have all privileges on the database with name specified below. We discourage the user to use a the MySQL root user or create a user with all privileges on all databases. Instead, we encourage creation of a new MySQL user solely for SDRAP. This can be done as follows:

1 log into the MySQL server with a user that has sufficient privileges to create users and grant privileges.

2 execute:
```mysql
CREATE USER '<username>'@'localhost'
IDENTIFIED BY '<password>';
```

3 then:
```mysql
GRANT ALL PRIVILEGES ON `<database-name>`.*
TO '<username>'@'localhost';
```

In the above two MySQL commands, replace `'localhost'` by `'%'` if intending for SDRAP to log into the MySQL server remotely.
Also, if you intend to use SDRAP multiple times, and do not want to grant privileges to each database individually, you can come up with a common prefix for all the databases SDRAP will create and grant all privileges on all databases with that prefix by replacing `<database-name>.*` by `<prefix>%.*`. Then, whenever specifying the *Database Name* SDRAP should use, make sure it has the specified prefix.
For security purposes, you should ensure that only the databases created by SDRAP on the MySQL server will have the specified prefix.

**Password** - the password of the MySQL user specified in the previous field.

**Database Name** - the name SDRAP should give the MySQL database which will be generated. The MySQL *Username* given above should have all privileges on this database, as explained in the *Username* parameter description above.


###### Organism Information
While these parameters are listed as required parameters, they can be left blank because they do not affect the computation. However, we highly recommend specifying the values of these parameters to be able to reference which organism the data refers to.

**Genus** - the name of the genus of the organism (taxonomic rank below family and above species)

**Species** - the name of the species of the organism (taxonomic rank below genus and above Strain)

**Strain** - the name of the strain of the organism (intraspecific taxonomic rank)

**Taxonomy ID** - the Taxonomy ID of the organism (taxon identifier in NCBI Taxonomy Database)


###### Genome Assemblies

**Precursor Genome** - precursor sequence file of the organism. See Genome Sequence Files above for more information on how to add files to the list of choices, how to remove them, and the necessary format of the files.

**Precursor Sequence Description Delimiter** - delimiter for the selected precursor sequence file. SDRAP will break the description line of each nucleotide sequence in the file into fields based on the specified delimiter and only consider the first field for future reference to the sequence. Please be sure to include a unique identifier as the first field in the description line of each nucleotide sequence across both the precursor and product sequence files.

**Product Genome** - product sequence file of the organism. See Genome Sequence Files above for more information on how to add files to the list of choices, how to remove them, and the necessary format of the files.

**Product Sequence Description Delimiter** - delimiter for the selected product sequence file. SDRAP will break the description line of each nucleotide sequence in the file into fields based on the specified delimiter and only consider the first field for future reference to the sequence. Please be sure to include a unique identifier as the first field in the description line of each nucleotide sequence across both the precursor and product sequence files.

**Telomere Pattern** - short nucleotide sequence whose repetition characterizes the telomeres of the organism. For example, if the telomeres of the organism consist of repetitions of the nucleotide sequence TTAGGG, then the value of the *Telomere Pattern* parameter should be specified as TTAGGG.


## Optional Parameters

###### Telomere Annotation Parameters
**Mismatch Ratio Limit** - (decimal number between 0 and 1; **DEFAULT**: 0.2; *e* in algorithmic description below) A low value for this parameter permits fewer mismatches relative to the current expansion *E* of the telomere at any step during the algorithm, while a high value allows for more mismatches. Setting the value of this parameter to 0, effectively forbids mismatches and leads to the discontinuation of the telomere expansion as soon as one mismatch is encountered. Setting the value of this parameter to 1 ignores mismatches while expanding the telomere as long as all other conditions are met.

**Limit of Excess Mismatches in Buffer** - (nonnegative integer; **DEFAULT**: 4; *h* in algorithmic description below) Generally, a low value for this parameter permits fewer mismatches close together without being "balanced out" by basepairs conforming to the ideal telomere. Setting the value of this parameter to 0, effectively forbids mismatches and leads to the discontinuation of the telomere expansion as soon as one mismatch is encountered. Setting the value of this parameter to a high number allows for longer substrings of the telomere to deviate from the ideal telomere.

**Maximum Length** - (nonnegative integer; **DEFAULT**: 100; *l* in algorithmic description below) The telomere cannot be expanded to a sequence longer than the value of this parameter. If the combined length of the buffer *B* and the current expansion *E* of the telomere exceed this maximum length, the buffer is discarded and expansion is halted. If the value of this parameter is set to an integer less than the length of the *Telomere Pattern*, the algorithm will never return a telomere.

**Maximum Distance to Sequence End** - (nonnegative integer; **DEFAULT**: 100; *o* in algorithmic description below) The distance between the telomere and the corresponding end of the sequence cannot exceed the value of this parameter. Setting the value of this parameter to 0 will forbid the presence of nontelomeric portions at those ends of the sequence where telomeres were found and lead to the exclusion of any such telomeres from the telomere annotation.

**Minimum Length** - (nonnegative integer; **DEFAULT**: 12; *m* in algorithmic description below) Any telomeric sequence found by the algorithm which does not have a length of at least the value of this parameter is excluded from the algorithm. If the value of this parameter is set to a value less than or equal to the length of the *Telomere Pattern*, the algorithm will only return telomeres at least as long as the *Telomere Pattern*.

To understand the meaning of these parameters and the effect their values have on the outcome of the computation, one may first need to understand the algorithm by which SDRAP annotates telomeres. The algorithm is described here at a high level (we use variables *o*, *l*, *e*, *h*, and *m* referenced in the description of the corresponding parameters above):
```
Find first occurrence of a cyclic permutation of *Telomere Pattern* at most *o* base pairs from the end of the DNA sequence and call it *E* (return no telomere if none was found)
One base pair adjacent to the current expansion *E* of the telomere at a time, do
 | add base pair to a buffer *B*
 | if the concatenation of *E* and *B* exceed *l*, break the loop
 | if the ratio between the Levenshtein distance *D* between the concatenation of E and B and an ideal telomeric sequence consisting of repititions of the *Telomere Pattern* over the combined length of *E* and *B* exceeds $e$, break the loop
 | if the number *N* of base pairs in $B$ whose addition caused the Levenshtein distance computed in the previous step to increase outnumber those *M* base pairs in *B* that didn't cause an increase by more than $h$, break the loop
 | if *M*>=*N*, add *E* to *B*
done
if *E* has length at least *m*, return *E* as the telomere
```


###### Arrangement Annotation Parameters
**Minimum HSP Length for Match Annotation** - (positive integer; **DEFAULT**: 18; *l* in algorithmic description below) The value of this parameter determines the minimum length a high-scoring pair provided by BLAST must have to be considered for match annotation.

**Minimum Bitscore for Preliminary Match Annotation** - (nonnegative integer; **DEFAULT**: 49; *b* in algorithmic description below) The value of this parameter determines the minimum bitscore a high-scoring pair provided by BLAST must have to be considered for preliminary match annotation.

**Minimum Percent Identity for Preliminary Match Annotation** - (decimal number between 0 and 100; **DEFAULT**: 99.00; *q* in algorithmic description below) The value of this parameter determines the minimum percent identity a high-scoring pair provided by BLAST must have to be considered for preliminary match annotation.

**Minimum Additional Base Pair Coverage to Qualify for Preliminary Match Annotation** - (nonnegative integer; **DEFAULT**: 4; *c* in algorithmic description below) The value of this parameter determines the number of base pairs a high-scoring pair provided by BLAST must cover, and which are not already covered by previously considered high-scoring pairs, or merged high-scoring pairs, to be considered for preliminary match annotation.

**Tolerance for Discrepancies Between Relative Positions of Precursor and Product Intervals for HSP Merging** - (nonnegative integer; **DEFAULT**: 8; *t* in algorithmic description below) The value of this parameter determines the maximum discrepancy between the relative positions of the corresponding end points of the precursor and product regions of two high-scoring pairs provided by BLAST, for them to be considered for merging.

**Maximum Gap Length for HSP Merging** - (nonnegative integer; **DEFAULT**: 3; *g* in algorithmic description below) The maximum number of basepairs between the precursor intervals and between the product intervals (separately) allowed for two matches corresponding to these intervals to be considered for merging.

**Minimum Base Pair Length for Gap Annotation** - (positive integer; **DEFAULT**: 4; *u* in algorithmic description below) The minimum number of basepairs between two consecutive product intervals of preliminary matches for the region to be annotated as a gap.

**Compute Pointer Annotations on Precursor and Product Sequences** - (checkbox; **DEFAULT**: checked) if checked, pointers will be computed; else they will not be computed.

**Minimum Base Pair Length for Pointer Annotation** - (positive integer; **DEFAULT**: 3; *v* in algorithmic description below) The minimum number of basepairs two consecutive product intervals of preliminary matches must overlap with for the overlapping region in the product sequence and the corresponding two regions in the precursor sequence to be annotated as a pointers.

**Minimum Bitscore for Additional Match Annotation** - (nonnegative integer; **DEFAULT**: 49; *b'* in algorithmic description below) The minimum bitscore a high-scoring pair provided by BLAST must have to be considered for additional match annotation.

**Minimum Percent Identity for Additional Match Annotation** - (decimal number between 0 and 100; **DEFAULT**: 80.00; *q'* in algorithmic description below) The minimum percent identity a high-scoring pair provided by BLAST must have to be considered for additional match annotation.

**Minimum Coverage of Product Interval for Additional Match Annotation** - (decimal number between 0 and 1; **DEFAULT**: 0.8; *r* in algorithmic description below) The minimum proportion of the product interval of a preliminary match covered by the product interval of a high-scoring pair provided by BLAST to be considered for annotation as additional match inheriting the index of the overlapping preliminary match.

**Minimum Coverage of Product Interval for Fragment Annotation** - (decimal number between 0 and 1; **DEFAULT**: 0.2; *s* in algorithmic description below) The minimum proportion of the product interval of a preliminary match covered by the product interval of a high-scoring pair provided by BLAST to be considered for annotation as fragment inheriting the index of the overlapping preliminary match.

To understand the meaning of these parameters and the effect their values have on the outcome of the computation, one may first need to understand the algorithm by which SDRAP computes the arrangements of the product sequences on the precursor sequences. The algorithm is described here at a high level (we use variables *l*, *t*, *g*, *b*, *q*, *b'*, *q'*, *c*, *u*, *v*, *b'*, *q'*, *r* and *s* referenced in the description of the corresponding parameters above). The algorithmic description is preceded with two definitions and broken into 3 steps.

To represent regions of high similarity, we define a *match* of a product sequence on a precursor sequence to be a triple ([*a*, *b*], [*c*, *d*], *o*), where [*a*, *b*] is an integer interval indicating a region in the precursor sequence, [*c*, *d*] is an integer interval indicating a region in the product sequence and *o* is either 0, or 1 indicating the relative orientation of the two regions (*o*=1 means the two regions have the same orientation and *o*=0 means the two regions are oppositely oriented). A high-scoring pair, which is returned by BLAST can be viewed as a *match* in the obvious way.

For two nonnegative integers *t* and *g*, we consider two matches ([*a1*, *b1*], [*c1*, *d1*], *o1*), and ([*a2*, *b2*], [*c2*, *d2*], *o2*) between the same precursor and product sequence to be *(t, g)-mergeable*, if the regions on the precursor sequence and the product sequence are at most *g* base pairs apart and one of following conditions is satisfied:
1 *o1*=*o2*=1 and |(*a1*-*a2*)-(*c1*-*c2*)|,|(*b1*-*b2*)-(*d1*-*d2*)| do not exceed *t*, or
2 *o1*=*o2*=0 and |(*a1*-*a2*)-(*d2*-*d1*)|,|(*b1*-*b2*)-(*c2*-*c1*)| do not exceed *t*.
Informally, two matches are *(t, g)-mergeable* if they are "roughly adjacent" at the same ends in the precursor and product, where the meaning of "roughly adjacent" depends on the values of the parameters *t* and *g*.

In the first part of the algorithm (applied to each pair of precursor and product sequences), a preliminary set of matches is extracted from the set of high-scoring pairs between a precursor and a product sequence provided by BLAST, but the algorithm enforces that the product region of no match in the set contains the product region of another.
In the second part, the preliminary matches are indexed according to the order in which their corresponding product regions are encountered when reading the product sequence from the 5' to the 3' end. Furthermore, pointers and gaps are annotated in this part.
In the third and last part, additional matches are extracted from the high-scoring pairs between the two sequences. These additional matches are artifically assigned the index of the preliminary match whose product region overlaps with their product region sufficiently (as defined below). Fragments of matches are annotated in this step, as well.


