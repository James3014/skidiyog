<?php
require('../includes/sdk.php');
require('../includes/auth.php'); // Admin authentication check



$filters = array(
    'date'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'expertise'   =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'park'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'article'        =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
);//_v($_POST);

$in = filter_var_array(array_merge($_REQUEST,$_POST), $filters);//_v($in);//exit();
/*
$in['date'] = empty($in['date']) ? date('Y-m-d') : $in['date'];
$in['expertise'] = empty($in['expertise']) ? 'sb' : $in['expertise'];
$in['instructor'] = empty($_POST['instructor']) ? [] : $_POST['instructor'];
*/
//var_dump($in);




$query_article ='';


?>
<!DOCTYPE html>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=false"/>
      
      <!--Import materialize.css-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
      <!--Import custom.css-->
      <link rel="stylesheet" href="../assets/css/custom.min.css">
      <!--Import jQuery-->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      
      <!--swiper-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/css/swiper.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.esm.bundle.js"></script>

      
    </head>

    <body>
      <header>
        <?php require('menu.php');?>
      </header>

    <!--form-->
    <form action="?q=yes" method="post" id="query_form">            
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w">
            <div class="row space-top-1 row-margin-b0">
              
              <div class="col s11 col-centered" id="private">


                <div class="input-field col s6">
                  <select class="icons article" name="article" id="article">
                      <option value="any">請選擇欲編輯之文章</option>
                      <?php
                        $ARTICLE = new ARTICLE();
                        $articles = $ARTICLE->listing();
                        foreach($articles as $n => $_a){
                      ?>
                        <option value="<?=$_a['idx']?>" data-icon="/photos/articles/<?=$_a['idx']?>/<?=$_a['idx']?>.jpg?v1009b" > <?=$_a['title']?> </option>
                      <?php } ?> 

                  </select>
                  
                </div>                
                
                <div class="input-field col s6">
                  <button data-target="add_article" class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">新增文章 <i class="material-icons">note_add</i></button>   
                </div>
              
              </div>
            </div>
          </div>
    </form>
    <!--form-->      


    <?php 
    if(!empty($in['article'])){
          $article_data = $ARTICLE->readByIdx($in['article']);                       
    ?> 
    
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w" style="z-index: 0">
            <div class="row space-top-1 row-margin-b0">              
              <form action="instructor_form.php" method="post" id="contentForm">
              <div class="col s11 col-centered" id="private2">
                <input type="hidden" name="cmd" id="cmd" value="article_update">
                <input type="hidden" name="action" id="action" value="">
                <input type="hidden" name="qidx" value="<?=$in['article']?>">                              
                <h2><?=($article_data['title']=='')?'請選擇文章':$article_data['title'];?></h2>
                <div class="row" style="margin-top:2rem;">                    
                        <h2 align="left">標題</h2>
                        <div class="input-field col s12">
                          <input type="text" name="title" id="title" value="<?=$article_data['title']?>">                          
                        </div>

                        <h2 align="left">內文</h2>
                        <div class="input-field col s12">
                          <textarea name="content" id="content" class="materialize-textarea"><?=$article_data['article']?></textarea>                          
                        </div>
                        <h2 align="left">雪場標籤</h2>
                        <div class="input-field col s12">
                          <textarea name="keyword" id="keyword" class="materialize-textarea"><?=$article_data['keyword']?></textarea>                          
                        </div>
                        <h2 align="left">關鍵字</h2>
                        <div class="input-field col s12">
                          <textarea name="tags" id="tags" class="materialize-textarea"><?=$article_data['tags']?></textarea>                          
                        </div>                                                
                </div>

                <button id="modifybt"  class="btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">修改 <i class="material-icons">chevron_right</i></button>                
              </div>
              </form>
            </div>
          </div>
    
    <?php
    }
    ?>
      
    <div id="add_article" class="modal center">  
    <form action="new_form.php" method="post" id="newForm">
          <div class="col s12 m6 offset-m1 pull-m3 header-block-content-w" style="z-index: 0">
            <div class="row space-top-1 row-margin-b0">              
              <div class="col s11 col-centered" id="private2">
                <input type="hidden" name="cmd" id="cmd" value="article_add">
                <input type="hidden" name="action" id="action" value="">
                <input type="hidden" name="qidx" value="<?=$in['article']?>">                                    
                <h2>新增文章</h2>
                <div class="row" style="margin-top:2rem;">                    
                        <h2 align="left">標題</h2>
                        <div class="input-field col s12">                          
                          <textarea name="new_title" id="new_title" class="materialize-textarea"></textarea> 
                        </div>

                        <h2 align="left">內文</h2>
                        <div class="input-field col s12">
                          <textarea name="new_content" id="new_content" class="materialize-textarea"></textarea>                      
                        </div>
                        <h2 align="left">雪場標籤</h2>
                        <div class="input-field col s12">
                          <textarea name="new_tags" id="new_tags" class="materialize-textarea"></textarea>                      
                        </div>
                        <h2 align="left">關鍵字</h2>
                        <div class="input-field col s12">
                          <textarea name="new_keyword" id="new_keyword" class="materialize-textarea"></textarea>                          
                        </div>                                                
                </div>

                <button id="addbt"  class="modal-close btn waves-effect waves-light btn-primary space-top-2 modal-trigger" type="submit" name="action">新增 <i class="material-icons">note_add</i></button>                
              </div>
            </div>
          </div>
    </form>
    </div>



      <footer>
        <div class="footer-copyright">
          <p class="center-align">© 2018 diy.ski</p>
        </div>
      </footer>

      <div id="success-msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">beenhere</i>
          <h4>修改成功</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p class="space-2">您所修改的資料己儲存成功。</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div>

      <div id="err_msg" class="modal center">
        <div class="modal-content">
          <i class="material-icons">sentiment_very_dissatisfied</i>
          <h4>Ooooops.....</h4>
          <div class="row space-top-2">
            <div class="input-field col s12 m8 col-centered">
              <p id="PERRMSG" class="space-2">....</p> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button href="#!" class="modal-close waves-effect btn btn-primary align-center">知道了 <i class="material-icons">check</i></button>
        </div>
      </div> 
      
      <!--JavaScript at end of body for optimized loading-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
      
      <!--custom js-->
      <script src="../assets/js/custom.js"></script>
      <script>
      $('.class .material-icons').hide();
      $('.class').click(function () {
        $(this).find('.class-m').toggleClass('class-m-active').find('.coach-name').toggle();
        $(this).find('.class-d').toggleClass('class-d-active');
        $(this).find('.material-icons').toggle();
        return false;
      });
      function _d(d){console.log(d)}
      function _a(a){alert(a)}      
      </script>


      <script>
      $(document).ready(function(){
        $('select.article').on('change',function(){          
            var article = $('#article').val();_d(article);            
            $("#query_form").submit();  
        });
   
        $('#modifybt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=article_update",                    
                    type: "POST",
                    data: $('#contentForm').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                        if(resp==101009){ // save ok                              
                             $('#success-msg').modal('open'); 
                        }else{
                            $('#PERRMSG').text('internal err: code='+resp);
                            $('#err_msg').modal('open');                                
                        }                         
                    }
                });
        });

        $('#addbt').on('click', function(e){         
                e.preventDefault();
                $.ajax({
                    //url: "account_info.php?act=up_2fcheck",
                    url: "post-cgi.php?cmd=article_add",                    
                    type: "POST",
                    data: $('#newForm').serialize(),                   
                    success: function(resp){
                        //alert("Successfully submitted."+resp)
                        if(resp==101009){ // save ok                              
                             $('#success-msg').modal('open'); 
                        }else{
                            $('#PERRMSG').text('internal err: code='+resp);
                            $('#err_msg').modal('open');                                
                        }                         
                    }
                });
        });        
           
        //alert('done');

               
      });
      </script>      

    </body>
  </html>