<?php

function create_parameter_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `parameter` (
    `parameter_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `date`                                TEXT            COMMENT 'date of running the pipeline with specified parameter values',
    `database`                            TEXT            COMMENT 'name of the mysql database',
    `username`                            TEXT            COMMENT 'mysql username used to run the pipeline',
    `genus`                               TEXT            COMMENT 'name of the genus containing the species whose genomes are contained in this database',
    `species`                             TEXT            COMMENT 'name of the species whose genomes are contained in this database',
    `strain`                              TEXT            COMMENT 'strain of the species whose genomes are contained in this database',
    `taxonomy_id`                         TEXT            COMMENT 'taxonomy id of the species whose genomes are contained in this database',
    `precursor_filename`                  TEXT            COMMENT 'path to the file containing the precursor genome contained in this database',
    `precursor_delimiter`                 TEXT            COMMENT 'delimiter in description lines of precursor genome file',
    `product_filename`                    TEXT            COMMENT 'path to the file containing the product genome contained in this database',
    `product_delimiter`                   TEXT            COMMENT 'delimiter in description lines of product genome file',
    `telo_pattern`                        TEXT            COMMENT 'repeated DNA sequence characterizing the telomere pattern of the species whose genomes are contained in this database',
    `telo_error_limit`                    DECIMAL(4,3)    COMMENT 'maximum number of erroneous basepairs relative to the current length of telomere allowed during telomere expansion',
    `telo_buffer_limit`                   INT(10)         COMMENT 'maximum number of erronerous basepairs in excess of non erroneous basepairs allowed in buffer during telomere expansion',
    `telo_max_length`                     INT(10)         COMMENT 'maximum length allowed for telomeres',
    `telo_max_offset`                     INT(10)         COMMENT 'maximum distance allowed between telomere and end of contig',
    `telo_min_length`                     INT(10)         COMMENT 'minimum length require_onced for telomeres',
    `hsp_min_length`                      INT(10)         COMMENT 'minimum length require_onced for HSPs',
    `pre_match_min_bitscore`              INT(10)         COMMENT 'minimum biscore require_onced for HSPs used for preliminary arrangement',
    `pre_match_min_pident`                DECIMAL(5,2)    COMMENT 'minimum percent identity require_onced for HSPs used for preliminary arrangement',  
    `pre_match_min_coverage_addition`     INT(10)         COMMENT 'minimum number of basepairs an HSP must cover and which are not already covered to be considered to become a preliminary match during computation of preliminary arrangement',
    `merge_tolerance`                     INT(10)         COMMENT 'maximum shift allowed between precursor segments and between product segments of two matches to still be considered for merging',
    `merge_max_gap`                       INT(10)         COMMENT 'maximum gap allowed between precursor segments and between product segments of two matches to still be considered for merging',
    `gap_min_length`                      INT(10)         COMMENT 'minimum length of non covered basepairs require_onced for a gap to be annotated',
    `compute_pointers`                    TINYINT(1)      COMMENT 'indicates whether or not pointers will be computed (1 means yes/true, 0 means no/false)',
    `pointer_min_length`                  INT(10)         COMMENT 'minimum length of overlap between product segments of preliminary matches require_onced for a pointer to be annotated',
    `add_match_min_bitscore`              INT(10)         COMMENT 'minimum bitscore require_onced for HSPs used for additional matches',
    `add_match_min_pident`                DECIMAL(5,2)    COMMENT 'minimum percent identity require_onced for HSPs used for additional matches',
    `add_match_min_prod_segment_overlap`  DECIMAL(4,3)    COMMENT 'minimum portion of product segment of preliminary match contained in product segment of additional match require_onced for additional match to be annotated as match with the index of that preliminary match',
    `fragment_min_prod_segment_overlap`   DECIMAL(4,3)    COMMENT 'minimum portion of product segment of preliminary match contained in product segment of HSP require_onced for HSP to be annotated as fragment with the index of that preliminary match',
    `property_min_coverage`               INT(10)         COMMENT 'minimum portion of part of product sequence between telomeres covered by product segments of preliminary matches of a precursor sequence require_onced for properties of the arrangement between the two sequences to be computed',
    `property_max_match_overlap`          INT(10)         COMMENT 'maximum overlap allowed between precursor segments of matches in an arrangement for the two matches to still be considered disjoint (if overlap as long as one of the precursor segments the two matches are not considered disjoint regardless of value of this parameter)',
    `property_clique_limit`               INT(10)         COMMENT 'maximum number of maximal cliques in match graph of an arrangement for which properties of the corresponding subarrangements are computed',
    `scr_complete`                        TINYINT(1)      COMMENT 'indicates whether or not completeness is require_onced for a non repeating and non p-overlapping subarrangement to be considered non scrambled (1 means yes/true, 0 means no/false)',
    `scr_consecutive`                     TINYINT(1)      COMMENT 'indicates whether or not consecutivenss is require_onced for a non repeating and non p-overlapping subarrangement to be considered non scrambled (1 means yes/true, 0 means no/false)',
    `scr_ordered`                         TINYINT(1)      COMMENT 'indicates whether or not orderedness is require_onced for a non repeating and non p-overlapping subarrangement to be considered non scrambled (1 means yes/true, 0 means no/false)',
    `output_min_coverage`                 INT(10)         COMMENT 'minimum portion of part of product sequence between telomeres covered by product segments of preliminary matches of a precursor sequence require_onced for annotations of precursor segments, product segments, fragments, gaps, and pointers to be included in output files',
    `output_give_summary`                 TINYINT(1)      COMMENT 'indicates whether the summary of interesting values should be included in the output (1 means yes/true, 0 means no/false)',
    `output_use_alias`                    TINYINT(1)      COMMENT 'indicates whether the program should reference sequences in its output by the identifier assigned by the program (1 means yes/true, 0 means no/false)',
    `output_gaps`                         TINYINT(1)      COMMENT 'indicates whether or not to output the annotations of gaps on the product sequences (1 means yes/true, 0 means no/false)',
    `output_fragments`                    TINYINT(1)      COMMENT 'indicates whether or not to output the annotations of fragments on the precursor sequences (1 means yes/true, 0 means no/false)',
    `output_give_complement`              TINYINT(1)      COMMENT 'indicates whether or not to output the annotations of intervals complementary to the precursor intervals of matches on the precursor sequences (1 means yes/true, 0 means no/false)',
    `output_min_complement_length`        TINYINT(1)      COMMENT 'minimum number of basepairs between two consecutive matches in the precursor for the interval to be annotated as match-complementary region'
  )  ENGINE = InnoDB 
     COMMENT 'table of user defined parameters used when running the pipeline';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_organism_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `organism` (
    `org_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `genus`        VARCHAR(50)                                 COMMENT 'genus of the organism',
    `species`      VARCHAR(50)                                 COMMENT 'species of the organism',
    `strain`       VARCHAR(50)                                 COMMENT 'strain of the organism',
    `taxonomy_id`  INT(10)                                     COMMENT 'ncbi taxonomy id'
  )  ENGINE = InnoDB
     COMMENT 'detailed information of the organism contained in this database';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_feature_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `feature` (
    `feat_id`      INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `type`         ENUM('prec', 'prod')                        COMMENT 'type of genetic feature',
    `table`        ENUM('nucleotide')                          COMMENT 'table where the feature resides'
  )  ENGINE = InnoDB
     COMMENT 'table of the different genetic features, e.g., precursor contig, or product contig';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_nucleotide_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `nucleotide` (
    `nuc_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `feat_id`      INT(10) NOT NULL                            COMMENT 'corresponding primary key of the `feature` table',
    `length`       INT(10)                                     COMMENT 'length of the nucleotide sequence',
    `sequence`     LONGTEXT                                    COMMENT 'full nucleotide sequence with the telomeres masked (as lower case characters)',
    FOREIGN KEY (`feat_id`) REFERENCES `feature`(`feat_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of nucleotide sequences';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_alias_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `alias` (
    `alias_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `nuc_id`         INT(10) NOT NULL               COMMENT 'primary key of the `nucleotide` table for the corresponding nucleotide sequence',
    `alias`          VARCHAR(200) NOT NULL          COMMENT 'name of the nucleotide sequence to cross reference',
    `is_primary`     TINYINT(1)                     COMMENT 'indicates whether the alias is the primary alias of the nucleotide sequence (1 means yes/true, 0 means no/false)',
    FOREIGN KEY (`nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of nucleotide sequences';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_telomere_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `telomere` (
    `tel_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `nuc_id`       INT(6)                                      COMMENT 'corresponding primary key of the `nucleotide` table',
    `five_start`   INT(10)                                     COMMENT 'starting position of the five prime telomere',
    `five_length`  INT(10)                                     COMMENT 'length of the five prime telomere',
    `three_start`  INT(10)                                     COMMENT 'starting position of the three prime telomere; set to length of sequence + 1 if length 0',
    `three_length` INT(10)                                     COMMENT 'length of the three prime telomere',
    FOREIGN KEY (`nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'start positions and lengths of the telomeres on the product sequences in the `nucleotide` table';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_hsp_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK, 
  "CREATE TABLE IF NOT EXISTS `hsp` (
    `hsp_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'primary key for the table',
    `org_id`        INT(10) NOT NULL                           COMMENT 'corresponding primary key of the `organism` table',
    `prec_nuc_id`   INT(10) NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prec_start`    INT(10)                                    COMMENT 'position of the first base pair of the precursor segment of the hsp in the corresponding precursor sequence',
    `prec_end`      INT(10)                                    COMMENT 'position of the last base pair of the precursor segment of the hsp in the corresponding precursor sequence',
    `prod_nuc_id`   INT(10) NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `prod_start`    INT(10)                                    COMMENT 'position of the first base pair of the product segment of the hsp in the corresponding product sequence',
    `prod_end`      INT(10)                                    COMMENT 'position of the last base pair of the product segment of the hsp in the corresponding product sequence',
    `orientation`   ENUM('+','-')                              COMMENT 'orientation of the hsp',
    `length`        INT(10)                                    COMMENT 'length of the hsp',
    `pident`        DECIMAL(5,2)                               COMMENT 'blast output: percent identity of the hsp',
    `mismatch`      INT(10)                                    COMMENT 'blast output: number of mismatches of the hsp',
    `evalue`        FLOAT                                      COMMENT 'blast output: evalue of the hsp',
    `bitscore`      INT(10)                                    COMMENT 'blast output: bitscore of the hsp',
    FOREIGN KEY (`org_id`) REFERENCES `organism`(`org_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of high scoring pairs found by blast between the precursor and product sequences in the `nucleotide` table and with minimum length as declared in the `parameters` table in the `hsp_min_length` column';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_match_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `match` (
    `match_id`        INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'primary key for the table',
    `prec_nuc_id`     INT(10) NOT NULL                         COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prod_nuc_id`     INT(10) NOT NULL                         COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `prec_start`      INT(10)                                  COMMENT 'position of the first base pair of the precursor segment of the match on the corresponding precursor sequence',
    `prec_end`        INT(10)                                  COMMENT 'position of the last base pair of the precursor segment of the match on the corresponding precursor sequence',
    `prod_start`      INT(10)                                  COMMENT 'position of first base pair of the product segment of the match on the corresponding product sequence',
    `prod_end`        INT(10)                                  COMMENT 'position of last base pair of the product segment of the match on the corresponding product sequence',
    `orientation`     ENUM('+','-')                            COMMENT 'orientation of the match',
    `length`          INT(10)                                  COMMENT 'length of the match',
    `hsp_id`          TEXT                                     COMMENT 'string representing a list of _ delimited primary keys of the entries in the `hsp` table that merged to become the match',
    `index`           INT(5)                                   COMMENT 'index of the match or fragment in the arrangement of the corresponding product on the corresponding precursor',
    `pre_cov`         DECIMAL(8,2)                             COMMENT 'percentage of product segment of preliminary match covered by product segment of additional match',
    `add_cov`         DECIMAL(8,2)                             COMMENT 'percentage of product segment of additional match covered by product segment of preliminary match',
    `is_preliminary`  TINYINT(1) DEFAULT '0'                   COMMENT 'indicates whether the match is a preliminary match (1 means yes/true, 0 means no/false)',
    `is_additional`   TINYINT(1) DEFAULT '0'                   COMMENT 'indicates whether the match is an additional match (1 means yes/true, 0 means no/false)',
    `is_fragment`     TINYINT(1) DEFAULT '0'                   COMMENT 'indicates whether the match is a fragment (1 means yes/true, 0 means no/false)',
    `prec_segment_alias`      VARCHAR(100) NOT NULL            COMMENT 'prefix of alias for the precursor segment of the match or fragment used in output files',
    `prod_segment_alias`      VARCHAR(100) NOT NULL            COMMENT 'prefix of alias for the product segment of the match or fragment used in output files',
     FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of preliminary matches, additional matches, and fragments computed by the pipeline from the high scoring pairs';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_gap_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `gap` (
    `gap_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'primary key for the table',
    `prec_nuc_id`   INT(10) NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prod_nuc_id`   INT(10) NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `index`        INT(5)                                      COMMENT 'index of the gap indicating order of occurrence on product sequence among all gaps on the product sequence with respect to the corresponding precursor sequence',
    `start`        INT(10)                                     COMMENT 'position of first base pair of the gap on corresponding product sequence',
    `end`          INT(10)                                     COMMENT 'position of last base pair of the gap on corresponding product sequence',
    `length`       INT(10)                                     COMMENT 'length of the gap',
    `is_terminal`  TINYINT(1) DEFAULT '0'                      COMMENT 'indicates whether the gap occurs at the ends of the part of the product sequence between the telomeres',
    `gap_alias`    VARCHAR(100) NOT NULL                       COMMENT 'prefix of alias for the gap used in output files',
     FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE =      InnoDB
     COMMENT 'table of gaps on the product sequences with respect to precursor sequences which do have matches with those product sequences';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_pointer_table( $LINK, array &$ERRORS ) {
  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `pointer` (
    `ptr_id`       INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'primary key for the table',
    `prec_nuc_id`              INT(10) NOT NULL                COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prod_nuc_id`              INT(10) NOT NULL                COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `left_match_id`            INT(10) NOT NULL                COMMENT 'primary key of the `match` table for the corresponding match flanking the pointer on the left on the corresponding product sequence',
    `right_match_id`           INT(10) NOT NULL                COMMENT 'primary key of the `match` table for the corresponding match flanking the pointer on the right on the corresponding product sequence',
    `prod_start`               INT(10) NOT NULL                COMMENT 'position of the first base pair of the pointer on the corresponding product sequence',
    `prod_end`                 INT(10) NOT NULL                COMMENT 'position of the last base pair of the pointer on the corresponding product sequence',
    `left_prec_start`          INT(10) NOT NULL                COMMENT 'position of the first base pair of the pointer at the end of the precursor segment of the left match on the corresponding precursor sequence',
    `left_prec_end`            INT(10) NOT NULL                COMMENT 'position of the last base pair of the pointer at the end of the precursor segment of the left match on the corresponding precursor sequence',
    `left_match_orientation`   ENUM('+','-')                   COMMENT 'orientation of left match',
    `right_prec_start`         INT(10) NOT NULL                COMMENT 'position of the first base pair of the pointer at the end of the precursor segment of the right match on the corresponding precursor sequence',
    `right_prec_end`           INT(10) NOT NULL                COMMENT 'position of the last base pair of the pointer at the end of the precursor segment of the right match on the corresponding precursor sequence',
    `right_match_orientation`  ENUM('+','-')                   COMMENT 'orientation of right match',
    `length`                   INT(10) NOT NULL                COMMENT 'length of the pointer',
    `is_preliminary`           TINYINT(1) DEFAULT '0'          COMMENT 'indicates whether the pointer is computed purely from preliminary matches (1 means yes/true, 0 means no/false)',
    `prod_alias`               VARCHAR(100)                    COMMENT 'prefix of alias for the pointer on the corresponding product sequence used in output files',
    `prec_left_alias`          VARCHAR(100)                    COMMENT 'prefix of alias for the pointer on the precursor sequence at the end of the precursor segment of the left match used in output files',
    `prec_right_alias`         VARCHAR(100)                    COMMENT 'prefix of alias for the pointer on the precursor sequence at the end of the precursor segment of the right match used in output files',
     FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`left_match_id`) REFERENCES `match`(`match_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`right_match_id`) REFERENCES `match`(`match_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of pointers and their locations on the precursor and product sequences';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_coverage_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `coverage` (
    `cov_id`        INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'primary key for the table',
    `prec_nuc_id`   INT(10)      NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prod_nuc_id`   INT(10)      NOT NULL                           COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `coverage`      DECIMAL(5,2) NOT NULL                           COMMENT 'percentage of the part of the corresponding product sequence between the telomeres covered by product segments of matches on the corresponding precursor sequence',
     FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of coverage data of product sequences by matches of precursor sequences';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

function create_properties_table( $LINK, array &$ERRORS ) {

  $result = mysqli_query( $LINK,
  "CREATE TABLE IF NOT EXISTS `properties` (
    `prop_id`             INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY  COMMENT 'primary key of the table',
    `prec_nuc_id`               INT(10) NOT NULL               COMMENT 'primary key of the `nucleotide` table for the corresponding precursor sequence',
    `prod_nuc_id`               INT(10) NOT NULL               COMMENT 'primary key of the `nucleotide` table for the corresponding product sequence',
    `preliminary_match_number`  INT(6) NOT NULL                COMMENT 'number of preliminary matches of the corresponding product sequence on the corresponding precursor sequence',
    `total_match_number`        INT(6) NOT NULL                COMMENT 'total number of matches of the corresponding prduct sequence on the corresponding precursor sequence',
    `non_gapped`                TINYINT(1)                     COMMENT 'indicates whether the arrangement has no gaps (1 means yes/true, 0 means no/false)',
    `non_overlapping`           TINYINT(1)                     COMMENT 'indicates whether the arrangement has no overlaps (1 means yes/true, 0 means no/false)',
    `non_repeating`             TINYINT(1)                     COMMENT 'indicates whether the arrangement has no repeats (1 means yes/true, 0 means no/false)',
    `exceeded_clique_limit`     TINYINT(1)                     COMMENT 'indicates whether the number of non repeating and non p overlapping subarrangements of the arrangement exceeded the limit found in the `parameter` table in the `property_clique_limit` column (1 means yes/true, 0 means no/false)',
    `weakly_complete`           TINYINT(1)                     COMMENT 'indicates whether the arrangement is weakly complete (1 means yes/true, 0 means no/false)',
    `strongly_complete`         TINYINT(1)                     COMMENT 'indicates whether the arrangement is strongly complete (1 means yes/true, 0 means no/false)',
    `weakly_consecutive`        TINYINT(1)                     COMMENT 'indicates whether the arrangement is weakly consecutive (1 means yes/true, 0 means no/false)',
    `strongly_consecutive`      TINYINT(1)                     COMMENT 'indicates whether the arrangement is strongly consecutive (1 means yes/true, 0 means no/false)',
    `weakly_ordered`            TINYINT(1)                     COMMENT 'indicates whether the arrangement is weakly ordered (1 means yes/true, 0 means no/false)',
    `strongly_ordered`          TINYINT(1)                     COMMENT 'indicates whether the arrangement is strongly ordered (1 means yes/true, 0 means no/false)',
    `weakly_non_scrambled`       TINYINT(1)                    COMMENT 'indicates whether the arrangement is weakly non scrambled (1 means yes/true, 0 means no/false)',
    `strongly_non_scrambled`     TINYINT(1)                    COMMENT 'indicates whether the arrangement is strongly non scrambled (1 means yes/true, 0 means no/false)',
     FOREIGN KEY (`prec_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE,
     FOREIGN KEY (`prod_nuc_id`) REFERENCES `nucleotide`(`nuc_id`) ON UPDATE CASCADE ON DELETE CASCADE
  )  ENGINE = InnoDB
     COMMENT 'table of properties for each arrangement with coverage above the threshold found in the `parameter` table in the `property_min_coverage` column';" );

  if ( $result === false ) {
    $ERRORS['other'][] = "MySQL error #" .  mysqli_connect_errno() . ": " . mysqli_connect_error() . " in " . basename(__FILE__,".php") . " near line " . __LINE__ . ".";
  }

  return $result;

}

?>