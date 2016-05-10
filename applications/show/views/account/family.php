<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">我的家族</a></li>
            <li><a href="#">加入的家族</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
             <!--<p>创建家族，请点击<a href="#">家族申请</a>填写家族申请表。</p>-->
             <p>当前家族等级：<em>Lv3</em>，可设<em>5</em>名长老，接纳<em>20</em>名家族主播，家族成员数上限3000人（<a href="#">家族等级说明</a>）</p>
             <table width="780" border="1" bordercolor="#DDDDDD">
      <tr bgcolor="#F5F5F5" class="biaot">
        <td width="150" height="40">家族名称</td>
        <td width="140" height="40">族长</td>
        <td width="120" height="40">族徽</td>
        <td width="65" height="40">家族长老</td>
        <td width="75" height="40">家族主播</td>
        <td width="90" height="40">家族成员</td>
        <td width="140" height="40">备注</td>
      </tr>
      
      <tr>
        <td height="30">24k金牌家族</td>
        <td height="30">爱手艺人</td>
        <td height="30"><img src="images/zuhui.jpg" title="" width="40" height="12" />&nbsp;305人佩戴</td>
        <td height="30">4/5</td>
        <td height="30">3/20</td>
        <td height="30">1835/3000</td>
        <td height="30"><a href="#">家族主页</a> | <a href="#">家族管理</a></td>
      </tr>
    </table>
            <div class="illustrate">
            <strong>家族说明：</strong><br/>
1、创建家族条件：富豪等级达到男爵级别或者魅力等级达到皇冠1级别。<br/>
2、每个用户最多同时成为5个家族的成员，每个主播最多同时成为3个家族的家族主播。<br/>
3、每人最多可以同时佩戴两枚家族徽章。<br/>
            </div>
           </div><!-- .cooper-list 我的家族 -->   
           
           <div class="cooper-list onhide" style="position:relative;">
              <ul class="family">
                 <li>
                    <div class="family_left">
                       <h2>姿色.酒吧</h2>
                       <img class="fam_img" src="pic/family_1.jpg" />
                       <p>族长：Tel姿色酒吧</p><p>家族徽章：<img src="images/purple.jpg" /></p><p>成立时间：2012-5-15</p><p>挂勋章成员总数：7</p>                      
                    </div><!-- .family_left -->
                    
                    <div class="seg"></div>
                    
                    <div class="family_middle">
                      <p><a href="#"><img src="images/exitfamily.jpg" /></a><p>家族角色:普通成员</p><p>勋章状态：已拥有&nbsp;<input name="" type="checkbox" value="" />佩戴显示</p><p>徽章到期日：2013-5-15</p> 
                    </div><!-- .family_middle -->
                    <div class="family_right">
                       <button value="进入家族首页">进入家族首页</button><br/><br/>
                       <button value="进入家族房间">进入家族房间</button>
                    </div><!-- .family_right -->
                 </li>
                 
                 <li>
                    <div class="family_left">
                       <h2>姿色.酒吧</h2>
                       <img class="fam_img" src="pic/family_1.jpg" />
                       <p>族长：Tel姿色酒吧</p><p>家族徽章：<img src="images/purple.jpg" /></p><p>成立时间：2012-5-15</p><p>挂勋章成员总数：7</p>                      
                    </div><!-- .family_left -->
                    
                    <div class="seg"></div>
                    
                    <div class="family_middle">
                      <p><a href="#"><img src="images/exitfamily.jpg" /></a><p>家族角色:普通成员</p><p>勋章状态：已拥有&nbsp;<input name="" type="checkbox" value="" />佩戴显示</p>
                      <p>徽章到期日：尚未购买&nbsp;<a href="#" id="showbuybox"><img src="images/buy.jpg" /></a></p> 
                    </div><!-- .family_middle -->
                    <div class="family_right">
                       <button value="进入家族首页">进入家族首页</button><br/><br/>
                       <button value="进入家族房间">进入家族房间</button>
                    </div><!-- .family_right -->
                 </li>
              </ul>
            <div class="illustrate">
            <strong>家族说明：</strong><br/>
1、创建家族条件：富豪等级达到男爵级别或者魅力等级达到皇冠1级别。<br/>
2、每个用户最多同时成为5个家族的成员，每个主播最多同时成为3个家族的家族主播。<br/>
3、每人最多可以同时佩戴两枚家族徽章。<br/>
            </div>
                      <div class="buy-box onhide" id="closebuybox">
                        <div class="tit"><a>购买家族徽章</a><a href="#" onclick="javascript:turnoff('closebuybox')" class="guanbi"></a></div>
                        <div class="con"><img src="images/purple.jpg" />&nbsp;&nbsp;徽章使用费5皮蛋/月，确定购买？<br/>每人最多可同时佩戴2个徽章<br/><br/><button value="确定购买">确定购买</button>  &nbsp;&nbsp;&nbsp;<button value="返回">返回</button></div>
                        
                      </div><!-- .buy-box -->
           </div><!-- .cooper-list 加入的家族 -->
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->



