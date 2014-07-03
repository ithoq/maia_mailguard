<?php
   /*
     * $Id$
     *
     * MAIA MAILGUARD LICENSE v.1.0
     *
     * Copyright 2004 by Robert LeBlanc <rjl@renaissoft.com>
     *                   David Morton <mortonda@dgrmm.net>
     * All rights reserved.
     *
     * PREAMBLE
     *
     * This License is designed for users of Maia Mailguard
     * ("the Software") who wish to support the Maia Mailguard project by
     * leaving "Maia Mailguard" branding information in the HTML output
     * of the pages generated by the Software, and providing links back
     * to the Maia Mailguard home page.  Users who wish to remove this
     * branding information should contact the copyright owner to obtain
     * a Rebranding License.
     *
     * DEFINITION OF TERMS
     *
     * The "Software" refers to Maia Mailguard, including all of the
     * associated PHP, Perl, and SQL scripts, documentation files, graphic
     * icons and logo images.
     *
     * GRANT OF LICENSE
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions
     * are met:
     *
     * 1. Redistributions of source code must retain the above copyright
     *    notice, this list of conditions and the following disclaimer.
     *
     * 2. Redistributions in binary form must reproduce the above copyright
     *    notice, this list of conditions and the following disclaimer in the
     *    documentation and/or other materials provided with the distribution.
     *
     * 3. The end-user documentation included with the redistribution, if
     *    any, must include the following acknowledgment:
     *
     *    "This product includes software developed by Robert LeBlanc
     *    <rjl@renaissoft.com>."
     *
     *    Alternately, this acknowledgment may appear in the software itself,
     *    if and wherever such third-party acknowledgments normally appear.
     *
     * 4. At least one of the following branding conventions must be used:
     *
     *    a. The Maia Mailguard logo appears in the page-top banner of
     *       all HTML output pages in an unmodified form, and links
     *       directly to the Maia Mailguard home page; or
     *
     *    b. The "Powered by Maia Mailguard" graphic appears in the HTML
     *       output of all gateway pages that lead to this software,
     *       linking directly to the Maia Mailguard home page; or
     *
     *    c. A separate Rebranding License is obtained from the copyright
     *       owner, exempting the Licensee from 4(a) and 4(b), subject to
     *       the additional conditions laid out in that license document.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS
     * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
     * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
     * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
     * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
     * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
     * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
     * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
     * TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
     * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     *
     */

  function pre_check_6() {
    global $dbh;
    $select = "SELECT * from maia_themes WHERE name=?";
    $sth = $dbh->query($select, "DGM");
    
    if (PEAR::isError($sth)) {
         $str = $sth->getMessage() . " = [" . $select . "]" ;
         return array(false, $str );
    }

    if ($sth->numRows() > 0) {
      return array(true,"");
    } else {
      return array(false, "DGM theme already removed, assuming renaming of other themes has occured");
    }
  }
  
  function upgrade_6() {
    global $dbh;
    
    $select = "SELECT * from maia_themes WHERE name=?";
    $sth = $dbh->query($select, "DGM");
    
    if (PEAR::isError($sth)) {
         $str = $sth->getMessage() . " = [" . $select . "]" ;
         return array(false, $str );
    }

    $row = $sth->fetchRow();
    
    $dgm_theme_id = $row['id'];
    
    $sth->free();
    
    $select = "SELECT * from maia_themes WHERE name LIKE 'Maia%Blue'";
    $sth = $dbh->query($select);
    
    if (PEAR::isError($sth)) {
         $str = $sth->getMessage() . " = [" . $select . "]" ;
         return array(false, $str );
    }

    $row = $sth->fetchRow();
    
    $default_theme_id = $row['id'];
    
    $sth->free();
     
    $updates = array( "UPDATE maia_themes SET name = 'Ocean Surf', path='ocean_surf' WHERE name LIKE  'Maia%Blue'",
                      "UPDATE maia_themes SET name = 'Desert Sand', path='desert_sand' WHERE name LIKE 'Mild%Brown'",
                      "UPDATE maia_users SET theme_id = $default_theme_id WHERE theme_id=$dgm_theme_id",
                      "DELETE FROM maia_themes WHERE id=$dgm_theme_id"
               );
    foreach ($updates as $sql) {
       $result = $dbh->query($sql);
       if (PEAR::isError($result)) {
         $str = $result->getMessage() . " = [" . $sql . "]" ;
         return array(false, $str );
       }
    }
    
    return array(true,"");
  }
  
  function post_check_6() {
    global $dbh;
    $select = "SELECT * from maia_themes WHERE name=?";
    $sth = $dbh->query($select, "Desert Sand");
    
    if (PEAR::isError($sth)) {
         $str = $sth->getMessage() . " = [" . $select . "]" ;
         return array(false, $str );
    }

    if ($sth->numRows() == 1) {
      return array(true,"");
    } else {
      return array(false, "Desert Sand not found in table.");
    }
    
  }
  
?>