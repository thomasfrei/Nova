<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Nova Installation Tests</title>

    <style type="text/css">
        body { width: 52em; margin: 0 auto; font-family:Helvetica; background: #fff; font-size: 1em; }
        .pass {background-color: #B7EAB1;border-radius: 5px;margin-bottom: 10px;height:50px;padding:5px;
            box-shadow:3px 4px 5px 0px #ccc;}
        .pass .sign {width:30px;height:30px;float:left;margin: 5px 20px 10px 10px;color: #188326;font-size: 2em;}
        .pass .desc{margin-top:5px;margin-bottom: 5px;font-size: 0.8em;color:#006F29;}

        .fail {background-color: #F8C1C1;border-radius: 5px;margin-bottom: 10px;height:auto;min-height:50px;padding:5px;
            box-shadow:3px 4px 5px 0px #ccc;}
        .fail .sign {width:30px;height:30px;float:left;margin: 5px 20px 10px 10px;color: #950202;font-size: 2em;}
        .fail .desc{margin-top:5px;margin-bottom: 5px;font-size: 0.8em;color:#E82828;}
        .fail .tip{height:auto;padding:10px;font-size:0.8em;font-weight:bold;margin:10px 50px 10px 50px;color:#884646;}
        .title{color:#884646;font-size: 1em;margin-top:5px;margin-bottom: 5px;font-weight: bold;}
        #result{background-color: #B7EAB1}
    </style>

</head>
<body>


<h1>Environment Tests</h1>

<?php 
    $results = runTests(); 
    displayResults($results); 
 ?>

</body>
</html>

<?php

    function runTests()
    {
        $results = array(
            'failed' => false,
            'php.version' => true,
            'directory.application' => true,
            'directory.library' => true,
        );

        // PHP version
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $results['php.version'] = false;
            $results['failed'] = true;
        }

        // Application Directory
        if(!is_dir(APPPATH)){
            $results['directory.application'] = false;
            $results['failed'] = true;
        }

        // Library
        if(!is_dir(SYSPATH)){
            $results['directory.library'] = false;
            $results['failed'] = true;
        }
        

        return $results;
    }

    function displayResults($results)
    {
        if ($results['failed'] === true){
            echo"<div class=\"fail\">
                <div class=\"sign\">&#10008;</div>
                <p class=\"title\">Test-Results:</p>
                <p class=\"desc\">Nova may <strong>not work</strong> Correctly in your Environment</p>
            </div>";
        } else {
            echo"<div class=\"pass\">
                <div class=\"sign\">&#10004;</div>
                <p class=\"title\">Test-Results:</p>
                <p class=\"desc\">Your Environment passed all Tests. Delete or rename the <strong>[".__FILE__."]</strong>   File and Refresh the Page</p>
            </div>";
        }

        echo "<p>The following Tests have been run to determine if Nova will work in your Environment:</p>    ";

        if($results['php.version'] === false) {
            echo "<div class=\"fail\">
                <div class=\"sign\">&#10008;</div>
                <p class=\"title\">PHP-Version:</p>
                <p class=\"desc\">Nova Requires PHP-Version <strong> 5.3.0.</strong> Your Version: <strong>" . PHP_VERSION . "</strong></p>
            </div>";
        } else {
            echo "<div class=\"pass\">
                <div class=\"sign\">&#10004;</div>
                <p class=\"title\">PHP-Version:</p>
                <p class=\"desc\">Nova Requires PHP-Version<strong>  5.3.0.</strong> Your Version: <strong>". PHP_VERSION . "</strong></p>
            </div>";
        }

        if($results['directory.application'] === false) {
            echo "<div class=\"fail\">
                <div class=\"sign\">&#10008;</div>
                <p class=\"title\">Application Directory:</p>
                <p class=\"desc\">Your Application Directory Path <strong>[".APPPATH."]</strong> is Incorrectly Configured</p>
                 <div class=\"tip\">
                    Open up the index.php file in your editor and configure the Application path.<br /> 
                    The path needs to be set relative to the Document Root.<br>
                    Document Root = [".DOCROOT."]
                 </div>
            </div>";
        } else {
            echo "<div class=\"pass\">
                <div class=\"sign\">&#10004;</div>
                <p class=\"title\">Application Directory:</p>
                <p class=\"desc\">Your Application Directory Path <strong>[".APPPATH."]</strong> is Correctly Configured</p>
            </div>";
        }

        if($results['directory.library'] === false) {
            echo "<div class=\"fail\">
                <div class=\"sign\">&#10008;</div>
                <p class=\"title\">Nova Library:</p>
                <p class=\"desc\">The Nova Library Path <strong>[".SYSPATH."]</strong> is Incorrectly Configured</p>
                 <div class=\"tip\">
                    Open up the index.php file in your editor and configure the library path.<br /> 
                    The path needs to be set relative to the Document Root.<br>
                    Document Root = [".DOCROOT."]
                 </div>
            </div>";
        } else {
            echo "<div class=\"pass\">
                <div class=\"sign\">&#10004;</div>
                <p class=\"title\">Nova Library:</p>
                <p class=\"desc\">The Nova Library Path <strong>[".SYSPATH."]</strong> is Correctly Configured</p>
            </div>";
        }
        
    }