    <html>
<head>
    <title><?=$this->title?></title>
        <style type="text/css">
        @import url(http://fonts.googleapis.com/css?family=Ubuntu);
        @import url(http://fonts.googleapis.com/css?family=Droid+Sans);
        body { margin:0;padding:0;}
        .header{width: 100%; height:80px; margin: 0px; padding-top:20px;  background:whiteSmoke;border-bottom: 1px solid lightGrey;box-shadow:0px 0px 14px 0px #ccc; font-size: 12pt; font-weight: lighter;color: lightGrey;}
        .exceptionType{padding: 20px 0 0 20px; font-family: Ubuntu; color:#B34949; font-weight: bold;font-size:18pt;
            display: inline;}
        .exceptionMsg{padding: 10px 0 0 20px; margin:0px; font-weight: lighter;font-size: 24px;font-family: Droid sans;
            color: black;}
        .details{width: 640;float: left;height:auto;margin: 30px 0 0 40px;box-shadow: -4px -4px 1px -1px #B34949;border: 1px solid lightGrey;border-top: 0;border-radius: 5px;}
        .details-head{background: whiteSmoke;width: 640px;height: 30px;border-radius: 5px 5px 0px 0px;border-top: 1px solid lightGrey;border-bottom: 1px solid lightGrey;text-align:center;}
        .details-head p{margin:5px; font-family: Droid;}
        .details-item{width: auto;height: auto;font-family: Droid Sans;padding:10px;border-bottom: 1px solid lightGrey;background: #FDFCFC; box-shadow: -0px 0px 20px 0px lightGrey;}
        .line-1{color:black;display:inline;}
        .line-2{color:#B15757;display:inline;}
        .line-3{color:gray;margin:0;font-size: 13px;}
        .details h3{font-family: Ubuntu; color:#B34949; font-weight: bold;}


    </style>
</head>
<body>
<div class="header">
    <h1 class="exceptionType"><?=$this->errorType?></h1>   at <?=$this->error->request->getRequestUri();?>
    <h2 class="exceptionMsg"><?=$this->errorMsg?></h2>
</div>

    
        <div class="details">
            <div class="details-head">
                <p>Error Details</p>
            </div>

            <div class="details-item">
                <p class ="line-1"><?=$this->errorType?></p>::<p class="line-2"><?=$this->errorMsg?></p>
                <p class="line-3">in <strong><?=$this->errorFile?></strong> at Line <strong><?=$this->errorLine?></strong></p>
            </div>
            <div class="details-item">
                <p class ="line-1">REQUEST-PARAMS::</p>
                <p class="line-3"><?php var_dump($this->params);?></p>
            </div>

         </div>
         <div class="details">
            <div class="details-head">
                <p>Stack Trace</p>
            </div>

            <? foreach($this->errorTrace as $error): ?>
            <div class="details-item">
                <p class ="line-1"><?=$error['class']?></p>::<p class="line-2"><?=$error['function']?>()</p>
                <p class="line-3">in <strong><?=$error['file']?></strong> at Line <strong><?=$error['line']?></strong> </p>
            </div>
            <? endforeach;?>
         </div>

</body>
</html>