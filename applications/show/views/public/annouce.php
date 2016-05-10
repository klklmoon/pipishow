<div class="w1000 mt20 boxshadow">
  <div class="announcement">
     <div class="title">
     <h2><?php echo $thread['title']?></h2>
      <br/>
      <span><?php echo date('Y-m-d H:i',$thread['create_time']) ?></span>
     </div>
     <div class="con">
     <?php echo $post['content']?>
     </div>
  </div>
</div><!-- .w1000 -->