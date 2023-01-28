<?php
include_once('class.pdf.php');

class Cezpdf extends Cpdf {
//==============================================================================
// this class will take the basic interaction facilities of the Cpdf class
// and make more useful functions so that the user does not have to 
// know all the ins and outs of pdf presentation to produce something pretty.
//
// IMPORTANT NOTE
// there is no warranty, implied or otherwise with this software.
// 
// version 008 (versioning is linked to class.pdf.php)
//
// released under a public domain licence.
//
// Wayne Munro, R&OS Ltd, http://www.ros.co.nz/pdf
//==============================================================================

var $ez=array('fontSize'=>10); // used for storing most of the page configuration parameters
var $y; // this is the current vertical positon on the page of the writing point, very important
var $ezPages=array(); // keep an array of the ids of the pages, making it easy to go back and add page numbers etc.
var $ezPageCount=0;

// ------------------------------------------------------------------------------

function Cezpdf($paper='a4',$orientation='portrait'){
  // Assuming that people don't want to specify the paper size using the absolute coordinates
  // allow a couple of options:
  // paper can be 'a4' or 'letter'
  // orientation can be 'portrait' or 'landscape'
  // or, to actually set the coordinates, then pass an array in as the first parameter.
  // the defaults are as shown.

  if (!is_array($paper)){
    switch (strtolower($paper)){
      case 'letter':
        $size = array(0,0,612,792);
        break;
      case 'a4':
      default:
        $size = array(0,0,595.4,842);
        break;
    }
    switch (strtolower($orientation)){
      case 'landscape':
        $a=$size[3];
        $size[3]=$size[2];
        $size[2]=$a;
        break;
    }
  } else {
    // then an array was sent it to set the size
    $size = $paper;
  }
  $this->Cpdf($size);
  $this->ez['pageWidth']=$size[2];
  $this->ez['pageHeight']=$size[3];
  
  // also set the margins to some reasonable defaults
  $this->ez['topMargin']=30;
  $this->ez['bottomMargin']=30;
  $this->ez['leftMargin']=30;
  $this->ez['rightMargin']=30;
  
  // set the current writing position to the top of the first page
  $this->y = $this->ez['pageHeight']-$this->ez['topMargin'];
  // and get the ID of the page that was created during the instancing process.
  $this->ezPages[1]=$this->getFirstPageId();
  $this->ezPageCount=1;
}

// ------------------------------------------------------------------------------
function ezInsertMode($status=1,$pageNum=1,$pos='before'){
  // puts the document into insert mode. new pages are inserted until this is re-called with status=0
  // by default pages wil be inserted at the start of the document
  switch($status){
    case '1':
      if (isset($this->ezPages[$pageNum])){
        $this->ez['insertMode']=1;
        $this->ez['insertOptions']=array('id'=>$this->ezPages[$pageNum],'pos'=>$pos);
      }
      break;
    case '0':
      $this->ez['insertMode']=0;
      break;
  }
}
// ------------------------------------------------------------------------------

function ezNewPage(){
  // make a new page, setting the writing point back to the top
  $this->y = $this->ez['pageHeight']-$this->ez['topMargin'];
  // make the new page with a call to the basic class.
  $this->ezPageCount++;
  if (isset($this->ez['insertMode']) && $this->ez['insertMode']==1){
    $id = $this->ezPages[$this->ezPageCount] = $this->newPage(1,$this->ez['insertOptions']['id'],$this->ez['insertOptions']['pos']);
    // then manipulate the insert options so that inserted pages follow each other
    $this->ez['insertOptions']['id']=$id;
    $this->ez['insertOptions']['pos']='after';
  } else {
    $this->ezPages[$this->ezPageCount] = $this->newPage();
  }
}

// ------------------------------------------------------------------------------

function ezSetMargins($top,$bottom,$left,$right){
  // sets the margins to new values
  $this->ez['topMargin']=$top;
  $this->ez['bottomMargin']=$bottom;
  $this->ez['leftMargin']=$left;
  $this->ez['rightMargin']=$right;
  // check to see if this means that the current writing position is outside the 
  // writable area
  if ($this->y > $this->ez['pageHeight']-$top){
    // then move y down
    $this->y = $this->ez['pageHeight']-$top;
  }
  if ( $this->y < $bottom){
    // then make a new page
    $this->ezNewPage();
  }
}  

// ------------------------------------------------------------------------------

function ezGetCurrentPageNumber(){
  // return the strict numbering (1,2,3,4..) number of the current page
  return $this->ezPageCount;
}

// ------------------------------------------------------------------------------

function ezStartPageNumbers($x,$y,$size,$pos='left',$pattern='{PAGENUM} of {TOTALPAGENUM}',$num=''){
  // put page numbers on the pages from here.
  // place then on the 'pos' side of the coordinates (x,y).
  // pos can be 'left' or 'right'
  // use the given 'pattern' for display, where (PAGENUM} and {TOTALPAGENUM} are replaced
  // as required.
  // if $num is set, then make the first page this number, the number of total pages will
  // be adjusted to account for this.
  // Adjust this function so that each time you 'start' page numbers then you effectively start a different batch
  // return the number of the batch, so that they can be stopped in a different order if required.
  if (!$pos || !strlen($pos)){
    $pos='left';
  }
  if (!$pattern || !strlen($pattern)){
    $pattern='{PAGENUM} of {TOTALPAGENUM}';
  }
  if (!isset($this->ez['pageNumbering'])){
    $this->ez['pageNumbering']=array();
  }
  $i = count($this->ez['pageNumbering']);
  $this->ez['pageNumbering'][$i][$this->ezPageCount]=array('x'=>$x,'y'=>$y,'pos'=>$pos,'pattern'=>$pattern,'num'=>$num,'size'=>$size);
  return $i;
}

// ------------------------------------------------------------------------------

function ezWhatPageNumber($pageNum,$i=0){
  // given a particular generic page number (ie, document numbered sequentially from beginning),
  // return the page number under a particular page numbering scheme ($i)
  $num=0;
  $start=1;
  $startNum=1;
  foreach($this->ez['pageNumbering'][$i] as $k=>$v){
    if ($k<=$pageNum){
      if (is_array($v)){
        // start block
        if (strlen($v['num'])){
          // a start was specified
          $start=$v['num'];
          $startNum=$k;
          $num=$pageNum-$startNum+$start;
        }
      } else {
        // stop block
        $num=0;
      }
    }
  }
  return $num;
}

// ------------------------------------------------------------------------------

function ezStopPageNumbers($stopTotal=0,$next=0,$i=0){
  // if stopTotal=1 then the totalling of pages for this number will stop too
  // if $next=1, then do this page, but not the next, else do not do this page either
  // if $i is set, then stop that particular pagenumbering sequence.
  if (!isset($this->ez['pageNumbering'])){
    $this->ez['pageNumbering']=array();
  }
  if ($stopTotal){
    $this->ez['pageNumbering'][$i][$this->ezPageCount]='stopt';
  } else {
    $this->ez['pageNumbering'][$i][$this->ezPageCount]='stop';
  }
  if ($next){
    $this->ez['pageNumbering'][$i][$this->ezPageCount].='n';
  }
}

// ------------------------------------------------------------------------------

function ezPRVTaddPageNumbers(){
  // this will go through the pageNumbering array and add the page numbers are required
  if (isset($this->ez['pageNumbering'])){
    $totalPages1 = $this->ezPageCount;
    $tmp1=$this->ez['pageNumbering'];
    $status=0;
    foreach($tmp1 as $i=>$tmp){
      // do each of the page numbering systems
      // firstly, find the total pages for this one
      $k = array_search('stopt',$tmp);
      if ($k && $k>0){
        $totalPages = $k-1;
      } else {
        $l = array_search('stoptn',$tmp);
        if ($l && $l>0){
          $totalPages = $l;
        } else {
          $totalPages = $totalPages1;
        }
      }
      foreach ($this->ezPages as $pageNum=>$id){
        if (isset($tmp[$pageNum])){
          if (is_array($tmp[$pageNum])){
            // then this must be starting page numbers
            $status=1;
            $info = $tmp[$pageNum];
            $info['dnum']=$info['num']-$pageNum;
          } else if ($tmp[$pageNum]=='stop' || $tmp[$pageNum]=='stopt'){
            // then we are stopping page numbers
            $status=0;
          } else if ($tmp[$pageNum]=='stoptn' || $tmp[$pageNum]=='stopn'){
            // then we are stopping page numbers
            $status=2;
          }
        }
        if ($status){
          // then add the page numbering to this page
          if (strlen($info['num'])){
            $num=$pageNum+$info['dnum'];
          } else {
            $num=$pageNum;
          }
          $total = $totalPages+$num-$pageNum;
          $pat = str_replace('{PAGENUM}',$num,$info['pattern']);
          $pat = str_replace('{TOTALPAGENUM}',$total,$pat);
          $this->reopenObject($id);
          switch($info['pos']){
            case 'right':
              $this->addText($info['x'],$info['y'],$info['size'],$pat);
              break;
            default:
              $w=$this->getTextWidth($info['size'],$pat);
              $this->addText($info['x']-$w,$info['y'],$info['size'],$pat);
              break;
          }
          $this->closeObject();
        }
        if ($status==2){
          $status=0;
        }
      }
    }
  }
}

// ------------------------------------------------------------------------------

function ezPRVTcleanUp(){
  $this->ezPRVTaddPageNumbers();
}

// ------------------------------------------------------------------------------

function ezStream($options=''){
  $this->ezPRVTcleanUp();
  $this->stream($options);
}

// ------------------------------------------------------------------------------

function ezOutput($options=0){
  $this->ezPRVTcleanUp();
  return $this->output($options);
}

// ------------------------------------------------------------------------------

function ezSetY($y){
  // used to change the vertical position of the writing point.
  $this->y = $y;
  if ( $this->y < $this->ez['bottomMargin']){
    // then make a new page
    $this->ezNewPage();
  }
}

// ------------------------------------------------------------------------------

function ezSetDy($dy,$mod=''){
  // used to change the vertical position of the writing point.
  // changes up by a positive increment, so enter a negative number to go
  // down the page
  // if $mod is set to 'makeSpace' and a new page is forced, then the pointed will be moved 
  // down on the new page, this will allow space to be reserved for graphics etc.
  $this->y += $dy;
  if ( $this->y < $this->ez['bottomMargin']){
    // then make a new page
    $this->ezNewPage();
    if ($mod=='makeSpace'){
      $this->y += $dy;
    }
  }
}

// ------------------------------------------------------------------------------

function ezPrvtTableDrawLines($pos,$gap,$x0,$x1,$y0,$y1,$y2,$col){
  $x0=1000;
  $x1=0;
  $this->setStrokeColor($col[0],$col[1],$col[2]);
  foreach($pos as $x){
    $this->line($x-$gap/2,$y0,$x-$gap/2,$y2);
    if ($x>$x1){ $x1=$x; };
    if ($x<$x0){ $x0=$x; };
  }
  $this->line($x0-$gap/2,$y0,$x1-$gap/2,$y0);
  if ($y0!=$y1){
    $this->line($x0-$gap/2,$y1,$x1-$gap/2,$y1);
  }
  $this->line($x0-$gap/2,$y2,$x1-$gap/2,$y2);
}

// ------------------------------------------------------------------------------

function ezPrvtTableColumnHeadings($cols,$pos,$maxWidth,$height,$gap,$size,&$y,$options=array()){
  // uses ezText to add the text, and returns the height taken by the largest heading

  $mx=0;
  foreach($cols as $colName=>$colHeading){
    $this->ezSetY($y);
    if (isset($options[$colName]) && isset($options[$colName]['justification'])){
      $justification = $options[$colName]['justification'];
    } else {
      $justification = 'left';
    }
    $this->ezText($colHeading,$size,array('aleft'=> $pos[$colName],'aright'=>($maxWidth[$colName]+$pos[$colName]),'justification'=>$justification));
    $dy=$y-$this->y;
    if ($dy>$mx){
      $mx=$dy;
    }
  }
  $y=$y-$height;
  $y -= $gap;
  
  return $mx;
}

// ------------------------------------------------------------------------------

function ezPrvtGetTextWidth($size,$text){
  // will calculate the maximum width, taking into account that the text may be broken
  // by line breaks.
  $mx=0;
  $lines = explode("\n",$text);
  foreach ($lines as $line){
    $w = $this->getTextWidth($size,$line);
    if ($w>$mx){
      $mx=$w;
    }
  }
  return $mx;
}

// ------------------------------------------------------------------------------

function ezTable(&$data,$cols='',$title='',$options=''){
  // add a table of information to the pdf document
  // $data is a two dimensional array
  // $cols (optional) is an associative array, the keys are the names of the columns from $data
  // to be presented (and in that order), the values are the titles to be given to the columns
  // $title (optional) is the title to be put on the top of the table
  //
  // $options is an associative array which can contain:
  // 'showLines'=> 0 or 1, default is 1 (1->alternate lines are shaded, 0->no shading)
  // 'showHeadings' => 0 or 1
  // 'shaded'=> 0 or 1, default is 1 (1->alternate lines are shaded, 0->no shading)
  // 'shadeCol' => (r,g,b) array, defining the colour of the shading, default is (0.8,0.8,0.8)
  // 'fontSize' => 10
  // 'textCol' => (r,g,b) array, text colour
  // 'titleFontSize' => 12
  // 'titleGap' => 5 , the space between the title and the top of the table
  // 'lineCol' => (r,g,b) array, defining the colour of the lines, default, black.
  // 'xPos' => 'left','right','center','centre',or coordinate, reference coordinate in the x-direction
  // 'xOrientation' => 'left','right','center','centre', position of the table w.r.t 'xPos' 
  // 'width'=> <number> which will specify the width of the table, if it turns out to not be this
  //    wide, then it will stretch the table to fit, if it is wider then each cell will be made
  //    proportionalty smaller, and the content may have to wrap.
  // 'maxWidth'=> <number> similar to 'width', but will only make table smaller than it wants to be
  // 'options' => array(<colname>=>array('justification'=>'left','width'=>100,'link'=>linkDataName),<colname>=>....)
  //             allow the setting of other paramaters for the individual columns
  //
  // note that the user will have had to make a font selection already or this will not 
  // produce a valid pdf file.
  
  if (!is_array($data)){
    return;
  }
  
  if (!is_array($cols)){
    // take the columns from the first row of the data set
    reset($data);
    list($k,$v)=each($data);
    if (!is_array($v)){
      return;
    }
    $cols=array();
    foreach($v as $k1=>$v1){
      $cols[$k1]=$k1;
    }
  }
  
  if (!is_array($options)){
    $options=array();
  }

  $defaults = array(
    'shaded'=>1,'showLines'=>1,'shadeCol'=>array(0.8,0.8,0.8),'fontSize'=>10,'titleFontSize'=>12
    ,'titleGap'=>5,'lineCol'=>array(0,0,0),'gap'=>5,'xPos'=>'centre','xOrientation'=>'centre'
    ,'showHeadings'=>1,'textCol'=>array(0,0,0),'width'=>0,'maxWidth'=>0,'cols'=>array());

  foreach($defaults as $key=>$value){
    if (is_array($value)){
      if (!isset($options[$key]) || !is_array($options[$key])){
        $options[$key]=$value;
      }
    } else {
      if (!isset($options[$key])){
        $options[$key]=$value;
      }
    }
  }

  $middle = ($this->ez['pageWidth']-$this->ez['rightMargin'])/2+($this->ez['leftMargin'])/2;
  // figure out the maximum widths of the text within each column
  $maxWidth=array();
  foreach($cols as $colName=>$colHeading){
    $maxWidth[$colName]=0;
  }
  // find the maximum cell widths based on the data
  foreach($data as $row){
    foreach($cols as $colName=>$colHeading){
      $w = $this->ezPrvtGetTextWidth($options['fontSize'],(string)$row[$colName])*1.01;
      if ($w > $maxWidth[$colName]){
        $maxWidth[$colName]=$w;
      }
    }
  }
  // and the maximum widths to fit in the headings
  foreach($cols as $colName=>$colTitle){
    $w = $this->ezPrvtGetTextWidth($options['fontSize'],(string)$colTitle)*1.01;
    if ($w > $maxWidth[$colName]){
      $maxWidth[$colName]=$w;
    }
  }
  
  // calculate the start positions of each of the columns
  $pos=array();
  $x=0;
  $t=$x;
  $adjustmentWidth=0;
  $setWidth=0;
  foreach($maxWidth as $colName => $w){
    $pos[$colName]=$t;
    // if the column width has been specified then set that here, also total the
    // width avaliable for adjustment
    if (isset($options['cols'][$colName]) && isset($options['cols'][$colName]['width']) && $options['cols'][$colName]['width']>0){
      $t=$t+$options['cols'][$colName]['width'];
      $maxWidth[$colName] = $options['cols'][$colName]['width']-$options['gap'];
      $setWidth += $options['cols'][$colName]['width'];
    } else {
      $t=$t+$w+$options['gap'];
      $adjustmentWidth += $w;
      $setWidth += $options['gap'];
    }
  }
  $pos['_end_']=$t;

  // if maxWidth is specified, and the table is too wide, and the width has not been set,
  // then set the width.
  if ($options['width']==0 && $options['maxWidth'] && ($t-$x)>$options['maxWidth']){
    // then need to make this one smaller
    $options['width']=$options['maxWidth'];
  }

  if ($options['width'] && $adjustmentWidth>0 && $setWidth<$options['width']){
    // first find the current widths of the columns involved in this mystery
    $cols0 = array();
    $cols1 = array();
    $xq=0;
    $presentWidth=0;
    $last='';
    foreach($pos as $colName=>$p){
      if (!isset($options['cols'][$last]) || !isset($options['cols'][$last]['width']) || $options['cols'][$last]['width']<=0){
        if (strlen($last)){
          $cols0[$last]=$p-$xq -$options['gap'];
          $presentWidth += ($p-$xq - $options['gap']);
        }
      } else {
        $cols1[$last]=$p-$xq;
      }
      $last=$colName;
      $xq=$p;
    }
    // $cols0 contains the widths of all the columns which are not set
    $neededWidth = $options['width']-$setWidth;
    // if needed width is negative then add it equally to each column, else get more tricky
    if ($presentWidth<$neededWidth){
      foreach($cols0 as $colName=>$w){
        $cols0[$colName]+= ($neededWidth-$presentWidth)/count($cols0);
      }
    } else {
    
      $cnt=0;
      while ($presentWidth>$neededWidth && $cnt<100){
        $cnt++; // insurance policy
        // find the widest columns, and the next to widest width
        $aWidest = array();
        $nWidest=0;
        $widest=0;
        foreach($cols0 as $colName=>$w){
          if ($w>$widest){
            $aWidest=array($colName);
            $nWidest = $widest;
            $widest=$w;
          } else if ($w==$widest){
            $aWidest[]=$colName;
          }
        }
        // then figure out what the width of the widest columns would have to be to take up all the slack
        $newWidestWidth = $widest - ($presentWidth-$neededWidth)/count($aWidest);
        if ($newWidestWidth > $nWidest){
          // then there is space to set them to this
          foreach($aWidest as $colName){
            $cols0[$colName] = $newWidestWidth;
          }
          $presentWidth=$neededWidth;
        } else {
          // there is not space, reduce the size of the widest ones down to the next size down, and we
          // will go round again
          foreach($aWidest as $colName){
            $cols0[$colName] = $nWidest;
          }
          $presentWidth=$presentWidth-($widest-$nWidest)*count($aWidest);
        }
      }
    }
    // $cols0 now contains the new widths of the constrained columns.
    // now need to update the $pos and $maxWidth arrays
    $xq=0;
    foreach($pos as $colName=>$p){
      $pos[$colName]=$xq;
      if (!isset($options['cols'][$colName]) || !isset($options['cols'][$colName]['width']) || $options['cols'][$colName]['width']<=0){
        if (isset($cols0[$colName])){
          $xq += $cols0[$colName] + $options['gap'];
          $maxWidth[$colName]=$cols0[$colName];
        }
      } else {
        if (isset($cols1[$colName])){
          $xq += $cols1[$colName];
        }
      }
    }

    $t=$x+$options['width'];
    $pos['_end_']=$t;
  }

  // now adjust the table to the correct location across the page
  switch ($options['xPos']){
    case 'left':
      $xref = $this->ez['leftMargin'];
      break;
    case 'right':
      $xref = $this->ez['pageWidth'] - $this->ez['rightMargin'];
      break;
    case 'centre':
    case 'center':
      $xref = $middle;
      break;
    default:
      $xref = $options['xPos'];
      break;
  }
  switch ($options['xOrientation']){
    case 'left':
      $dx = $xref-$t;
      break;
    case 'right':
      $dx = $xref;
      break;
    case 'centre':
    case 'center':
      $dx = $xref-$t/2;
      break;
  }
  foreach($pos as $k=>$v){
    $pos[$k]=$v+$dx;
  }
  $x0=$x+$dx;
  $x1=$t+$dx;
  
  // ok, just about ready to make me a table
  $this->setColor($options['textCol'][0],$options['textCol'][1],$options['textCol'][2]);
  $this->setStrokeColor($options['shadeCol'][0],$options['shadeCol'][1],$options['shadeCol'][2]);

  $middle = ($x1+$x0)/2;
  // if the title is set, then do that
  if (strlen($title)){
    $w = $this->getTextWidth($options['titleFontSize'],$title);
    $this->y -= $this->getFontHeight($options['titleFontSize']);
    if ($this->y < $this->ez['bottomMargin']){
      $this->ezNewPage();
      $this->y -= $this->getFontHeight($options['titleFontSize']);
    }
    $this->addText($middle-$w/2,$this->y,$options['titleFontSize'],$title);
//    $this->addText($xref + $t/2 - $w/2,$this->y,$options['titleFontSize'],$title);
    $this->y -= $options['titleGap'];
  }

  $y=$this->y; // to simplify the code a bit
  
  // make the table
  $height = $this->getFontHeight($options['fontSize']);
  $decender = $this->getFontDecender($options['fontSize']);

//  $y0=$y+$height+$decender;
  $y0=$y+$decender;
  $dy=0;
  if ($options['showHeadings']){
    $mx = $this->ezPrvtTableColumnHeadings($cols,$pos,$maxWidth,$height,$options['gap'],$options['fontSize'],$y,$options['cols']);
    $dy=$options['gap']/2;
    $y -= ($mx-$height);
  }
  $y1 = $y+$decender+$dy;

  // open an object here so that the text can be put in over the shading
  if ($options['shaded']){
    $this->saveState();
    $textObjectId = $this->openObject();
    $this->closeObject();
    $this->addObject($textObjectId);
    $this->reopenObject($textObjectId);
  }
  
  $cnt=0;
  $newPage=0;
  foreach($data as $row){
    $cnt++;
    $newRow=1;
    while($newPage || $newRow){
      $newRow=0;
      $y-=$height;
      if ($newPage || $y<$this->ez['bottomMargin']){
        $y2=$y+$height+$decender;
        if ($options['showLines']){
          $this->ezPrvtTableDrawLines($pos,$options['gap'],$x0,$x1,$y0,$y1,$y2,$options['lineCol']);
        }
        if ($options['shaded']){
          $this->closeObject();
          $this->restoreState();
        }
        $this->ezNewPage();
  
        if ($options['shaded']){
          $this->saveState();
          $textObjectId = $this->openObject();
          $this->closeObject();
          $this->addObject($textObjectId);
          $this->reopenObject($textObjectId);
        }
        $this->setColor($options['textCol'][0],$options['textCol'][1],$options['textCol'][2],1);
        $y = $this->ez['pageHeight']-$this->ez['topMargin'];
        $y0=$y+$decender;
        $mx=0;
        if ($options['showHeadings']){
          $mx = $this->ezPrvtTableColumnHeadings($cols,$pos,$maxWidth,$height,$options['gap'],$options['fontSize'],$y,$options['cols']);
          $y -= ($mx-$height);
          $y1=$y+$decender+$options['gap']/2;
        } else {
          $y1=$y0;
        }
        $y -= $height;
      }
      // write the actual data
      $mx=0;
      // if these cells need to be split over a page, then $newPage will be set, and the remaining
      // text will be placed in $leftOvers
      $newPage=0;
      $leftOvers=array();
  
      foreach($cols as $colName=>$colTitle){
        $this->ezSetY($y+$height);
        $colNewPage=0;
        if (isset($row[$colName])){
          if (isset($options['cols'][$colName]) && isset($options['cols'][$colName]['link']) && strlen($options['cols'][$colName]['link'])){
            
            $lines = explode("\n",$row[$colName]);
            if (isset($row[$options['cols'][$colName]['link']]) && strlen($row[$options['cols'][$colName]['link']])){
              foreach($lines as $k=>$v){
                $lines[$k]='<c:alink:'.$row[$options['cols'][$colName]['link']].'>'.$v.'</c:alink>';
              }
            }
          } else {
            $lines = explode("\n",$row[$colName]);
          }
        } else {
          $lines = array();
        }
        foreach ($lines as $line){
          $start=1;
          while (strlen($line) || $start){
            $start=0;
            $this->y=$this->y-$height;
            if ($this->y < $this->ez['bottomMargin']){
  //            $this->ezNewPage();
              $newPage=1;  // whether a new page is required for any of the columns
              $colNewPage=1; // whether a new page is required for this column
            }
            if ($colNewPage){
              if (isset($leftOvers[$colName])){
                $leftOvers[$colName].="\n".$line;
              } else {
                $leftOvers[$colName] = $line;
              }
              $line='';
            } else {
              if (isset($options['cols'][$colName]) && isset($options['cols'][$colName]['justification']) ){
                $just = $options['cols'][$colName]['justification'];
              } else {
                $just='left';
              }

              $line=$this->addTextWrap($pos[$colName],$this->y,$maxWidth[$colName],$options['fontSize'],$line,$just);
            }
          }
        }
  
        $dy=$y+$height-$this->y;
        if ($dy>$mx){
          $mx=$dy;
        }
      }
      // set $row to $leftOvers so that they will be processed onto the new page
      $row = $leftOvers;
      // now add the shading underneath
      if ($options['shaded'] && $cnt%2==0){
        $this->closeObject();
        $this->setColor($options['shadeCol'][0],$options['shadeCol'][1],$options['shadeCol'][2],1);
        $this->filledRectangle($x0-$options['gap']/2,$y+$decender+$height-$mx,$x1-$x0,$mx);
        $this->reopenObject($textObjectId);
      }
  
    } // end of while 
    $y=$y-$mx+$height;
  } // end of foreach ($data as $row)
  $y2=$y+$decender;
  if ($options['showLines']){
    $this->ezPrvtTableDrawLines($pos,$options['gap'],$x0,$x1,$y0,$y1,$y2,$options['lineCol']);
  }

  // close the object for drawing the text on top
  if ($options['shaded']){
    $this->closeObject();
    $this->restoreState();
  }
  
  $this->y=$y;
  return $y;
}

// ------------------------------------------------------------------------------

function ezText($text,$size=0,$options=''){
  // this will add a string of text to the document, starting at the current drawing
  // position.
  // it will wrap to keep within the margins, including optional offsets from the left
  // and the right, if $size is not specified, then it will be the last one used, or
  // the default value (12 I think).
  // the text will go to the start of the next line when a return code "\n" is found.
  // possible options are:
  // 'left'=> number, gap to leave from the left margin
  // 'right'=> number, gap to leave from the right margin
  // 'aleft'=> number, absolute left position (overrides 'left')
  // 'aright'=> number, absolute right position (overrides 'right')
  // 'justification' => 'left','right','center','centre','full'

  // only set one of the next two items (leading overrides spacing)
  // 'leading' => number, defines the total height taken by the line, independent of the font height.
  // 'spacing' => a real number, though usually set to one of 1, 1.5, 2 (line spacing as used in word processing)
  
  if(!$options) $options=Array();
  if (isset($options['aleft'])){
    $left=$options['aleft'];
  } else {
    $left = $this->ez['leftMargin'] + (isset($options['left'])?$options['left']:0);
  }
  if (isset($options['aright'])){
    $right=$options['aright'];
  } else {
    $right = $this->ez['pageWidth'] - $this->ez['rightMargin'] - (isset($options['right'])?$options['right']:0);
  }
  if ($size<=0){
    $size = $this->ez['fontSize'];
  } else {
    $this->ez['fontSize']=$size;
  }
  
  if (isset($options['justification'])){
    $just = $options['justification'];
  } else {
    $just = 'left';
  }
  
  // modifications to give leading and spacing based on those given by Craig Heydenburg 1/1/02
  if (isset($options['leading'])) { ## use leading instead of spacing
    $height = $options['leading'];
	} else if (isset($options['spacing'])) {
    $height = $this->getFontHeight($size) * $options['spacing'];
	} else {
		$height = $this->getFontHeight($size);
	}

  
  $lines = explode("\n",$text);
  foreach ($lines as $line){
    $start=1;
    while (strlen($line) || $start){
      $start=0;
      $this->y=$this->y-$height;
      if ($this->y < $this->ez['bottomMargin']){
        $this->ezNewPage();
      }
      $line=$this->addTextWrap($left,$this->y,$right-$left,$size,$line,$just);
    }
  }

  return $this->y;
}

// ------------------------------------------------------------------------------

// note that templating code is still considered developmental - have not really figured
// out a good way of doing this yet.
function loadTemplate($templateFile){
  // this function will load the requested template ($file includes full or relative pathname)
  // the code for the template will be modified to make it name safe, and then stored in 
  // an array for later use
  // The id of the template will be returned for the user to operate on it later
  if (!file_exists($templateFile)){
    return -1;
  }

  $code = implode('',file($templateFile));
  if (!strlen($code)){
    return;
  }

  $code = trim($code);
  if (substr($code,0,5)=='<?php'){
    $code = substr($code,5);
  }
  if (substr($code,-2)=='?>'){
    $code = substr($code,0,strlen($code)-2);
  }
  if (isset($this->ez['numTemplates'])){
    $newNum = $this->ez['numTemplates'];
    $this->ez['numTemplates']++;
  } else {
    $newNum=0;
    $this->ez['numTemplates']=1;
    $this->ez['templates']=array();
  }

  $this->ez['templates'][$newNum]['code']=$code;

  return $newNum;
}

// ------------------------------------------------------------------------------

function execTemplate($id,$data=array(),$options=array()){
  // execute the given template on the current document.
  if (!isset($this->ez['templates'][$id])){
    return;
  }
  eval($this->ez['templates'][$id]['code']);
}

// ------------------------------------------------------------------------------

function alink($info){
  // a callback function to support the formation of clickable links within the document
  switch($info['status']){
    case 'start':
    case 'sol':
      // the beginning of the link
      // this should contain the URl for the link as the 'p' entry, and will also contain the value of 'nCallback'
      if (!isset($this->ez['links'])){
        $this->ez['links']=array();
      }
      $i = $info['nCallback'];
      $this->ez['links'][$i] = array('x'=>$info['x'],'y'=>$info['y'],'decender'=>$info['decender'],'height'=>$info['height'],'url'=>$info['p']);
      $this->saveState();
      $this->setColor(0,0,1);
      $this->setStrokeColor(0,0,1);
      break;
    case 'end':
    case 'eol':
      // the end of the link
      // assume that it is the most recent opening which has closed
      $i = $info['nCallback'];
      $start = $this->ez['links'][$i];
      // add underlining
      $this->line($start['x'],$start['y']-2,$info['x'],$start['y']-2);
      $this->addLink($start['url'],$start['x'],$start['y']+$start['decender'],$info['x'],$start['y']+$start['decender']+$start['height']);
      $this->restoreState();
      break;
  }
}

// ------------------------------------------------------------------------------

}
?>