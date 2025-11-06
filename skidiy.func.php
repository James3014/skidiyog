<?php
require('includes/sdk.php');
?>

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

function _d(d){console.log(d)}
function _a(a){alert(a)}

//---------------[1]sidx [2]date [3]slot [4]park [5]instructor [6]expertise [7]ruleIdx [8]start [9]end [10]day [11]slot
      var regexpRule = /x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z0-9]+),e=(.+),ri=(.+),rs=(.+),re=(.+),rc=(\d+)d(\d+)c/i;
      var regexpCust = /x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z0-9]+),e=(.+)/i;
      var regexpPark = /.+,p=(.+?),.+/i;
      var regexpInstructor = /.+,i=(.+?),.+/i;
      var instructorOrders = {};
      var instructorZindex = 1;
      var ruleColor = ['#900000','#5ab2ec','#ffc443','#ff5a6e','#cdb38b','#55c2b7','#ffc0cb','#ccccff','#a4a527','#c31aff'];
      var bookedLessons = 0;
      var rulebookable = true;
      var bookedPark = '';
      var reservedInstructor = '';

      function sizeof(obj){
        var i = 0;
        for(var key in obj){
          ++i;
        }
        return i;
      }
      function arr2obj(arr){
        var lessonObj = {
          sidx: arr[1],
          date: arr[2],
          slot: arr[3],
          park: arr[4],
          instructor: arr[5],
          expertise: arr[6],
          ruleIdx: arr[7],
          ruleStart: arr[8],
          ruleEnd: arr[9],
          ruleDay: arr[10],
          ruleCnt: arr[11]
        }
        return lessonObj;
      }
      function schedule2lesson(schedule){
        var type = '';
        if(regexpRule.exec(schedule)!=null){
          type = 'rule';
          lessonArr = regexpRule.exec(schedule);
        }else if(regexpCust.exec(schedule)!=null){//_a(regexpRule);
          type = 'fixed';
          lessonArr = regexpCust.exec(schedule);
        }else{
          _a(schedule+"\n課程讀取發生異常！");
        }//_d(type);_d(lessonArr);
        return arr2obj(lessonArr);
      }

      function getInstructorPriority(){
        var _distinctInstructors = [];
        $("[schedule]").each(function(){//_d($(this).attr('schedule'));
          var _thisInstructor = regexpInstructor.exec($(this).attr('schedule'));//_d(_thisInstructor);
          if(typeof _distinctInstructors[_thisInstructor[1]]==="undefined"){
            if(instructorInfo[_thisInstructor[1]].priority>=1000){
              instructorInfo[_thisInstructor[1]].priority += 1000;
            }else{
              instructorInfo[_thisInstructor[1]].priority = Math.floor(Math.random()*1000)+1;
            }
            _distinctInstructors[_thisInstructor[1]] = instructorInfo[_thisInstructor[1]].priority;
          }
        });//_d(_distinctInstructors);
        return _distinctInstructors;
      }

      function getParkinSchecule(schedule){
        var regexpArr = regexpPark.exec(schedule);//_d(regexpArr);
        return regexpArr[1];
      }

      function getInstructorinSchecule(schedule){
        var regexpArr = regexpInstructor.exec(schedule);//_d(regexpArr);
        return regexpArr[1];
      }

      function checkSamePark(thisPark){
        if(bookedPark==''){
          bookedPark=thisPark;
          return true;
        }else{
          return (bookedPark===thisPark);
        }
      }

      function checkSameInstructor(thisInstructor){
        if(reservedInstructor==''){
          reservedInstructor=thisInstructor;
          return true;
        }else{
          return (reservedInstructor===thisInstructor);
        }
      }

      function checkLessonsInDays(data){_d(data);
        rulebookable = true;

        for (var ruleIdx in data){_d(ruleIdx);
          var dates = data[ruleIdx].dates;//_d(dates);
          var day = data[ruleIdx].day;
          var cnt = data[ruleIdx].cnt;
          var start = data[ruleIdx].start;
          var end = data[ruleIdx].end;
          var park = data[ruleIdx].park;
          var dateTS = [];
          var dateTScnt = {};
          var dateTSday = {};
          var instructor = data[ruleIdx].instructor;

          //{2018-09-30: 2, 2019-01-01: 1, 2018-12-03: 1}
          for (var d in dates) {
            var ts = Date.parse(d)/1000;//_d(d+'='+ts);
            dateTS.push(ts);
            dateTScnt[ts] = dates[d];//cnt
            dateTSday[ts] = d;//day
          }//for dates
          dateTS.sort();//_d(dateTScnt);
          //{1538265600000: 2, 1546300800000: 1, 1543795200000: 1}

          var firstDateTS = dateTS[0];
          var PointDateTS = dateTS[0];
          var totalCnt = 0;
          for (var i=1;i<=day;i++) {
            if ( typeof dateTScnt[PointDateTS] != "undefined" ){
              totalCnt += dateTScnt[PointDateTS];
            }
            PointDateTS += 24*60*60;//next day
          }//_d(totalCnt);
          if (totalCnt<cnt) {
            var allDayNote = '';
            if(parkInfo[park].courseHours!=2){
              allDayNote = '請注意：' + parkInfo[park].cname + '雪場<b>一堂課為' + parkInfo[park].courseHours + '小時</b>。';
            }
            if(park=='doraemon'){
              allDayNote += '遇假日需先預約好場地再預約課程。';
            }

            var notify = allDayNote + '<br>此堂需調度' + instructor.toUpperCase() + '教練，<b>' + start + '～' + end + '</b>這期間內<b>需連續'+day+'天</b>，選課達<b>' + cnt + '</b>堂以上才會開課。' 
                 + '目前還差<b>' + (cnt-totalCnt) + '</b>堂喔～';
            $("#classMsg").html(notify);
            rulebookable = false;
            _d('rulebookable='+rulebookable);
          }
        }//for rules
        
        return rulebookable;
      }//checkLessonsInDays

      function showSummary(){
        var lessonSummary = {'date':[], 'instructor':[], 'park':null};
        var note = '';
        bookedLessons = 0;//重置
        
        $(".booked").each(function(){
          bookedLessons += 1;
          var lesson = schedule2lesson($(this).attr('schedule'));//_d('showSummary: '+lesson.park);
          lessonSummary.park = parkInfo[lesson.park]['cname'];
          (lessonSummary.date.indexOf(lesson.date) === -1) ? lessonSummary.date.push(lesson.date) : '';
          (lessonSummary.instructor.indexOf(instructorInfo[lesson.instructor]['cname']) === -1) ? lessonSummary.instructor.push(instructorInfo[lesson.instructor]['cname']) : '';
          if(parkInfo[lesson.park].courseHours!=2){
            note = '請注意：' + lessonSummary.park + '雪場<b>一堂課為' + parkInfo[lesson.park].courseHours + '小時</b>。';
          }
          if(lesson.park=='doraemon'){
            note += '遇假日需先預約好場地再預約課程。';
          }
        });//_d(lessonSummary);

        if(bookedLessons>=1){
          $('#classNum').html(bookedLessons);
          if(rulebookable){           
            $('#classMsg').html(note + '上課日期: ' + lessonSummary.date.join(', ') + ' 共<b>' + lessonSummary.date.length + '</b>天.' + '<br>' + 
                                '雪場: ' + lessonSummary.park + '. 教練: ' + lessonSummary.instructor.join(', ') + '<br>' + 
                                '確認無誤後就可以下一步嘍～');
            $('#bookingBtnDiv').show();
          }else{
            $('#bookingBtnDiv').hide();
          }
        }else{
          bookedPark = reservedInstructor = '';
          $('#classNum').html('0');
          $('#classMsg').html('請點選上述日期與時段已完成訂課。');
          $('#bookingBtnDiv').hide();
        }
      }

      function checkRuleLessons(){
        var rules = {};

        //收集所有訂課的條件
        $(".booked").each(function(){
          //x=123,d=2018-09-30,p=naeba,s=1,i=amber,e=sb,ri=124,rs=2018-09-30,re=2018-10-10,rc=3d2s
          var schedule = $(this).attr('schedule');//_d(schedule);
          var lessonArr = regexpRule.exec(schedule);//_d(lessonArr);
          
          //指定開課不檢查
          if (lessonArr == null){
            return true;
          }
          var lesson = arr2obj(lessonArr);//_d(lesson);
          // {sidx: "123", date: "2018-09-30", park: "naeba", slot: "1", instructor: "amber", expertise:"sb", ruleIdx: "124", ruleStart: "2018-09-30", ruleEnd: "2018-10-10", ruleDay: "3", ruleCnt: "2"}
          if (typeof rules[lesson.ruleIdx] == "undefined") {
            rules[lesson.ruleIdx]={};
            rules[lesson.ruleIdx]["dates"]={};
          }
          //累計同一天的堂數
          if (typeof rules[lesson.ruleIdx]["dates"][lesson.date] != "undefined") {
            rules[lesson.ruleIdx]["dates"][lesson.date]+=1;
          }else{
            rules[lesson.ruleIdx]["dates"][lesson.date]=1;
          }
          //儲存條件
          rules[lesson.ruleIdx]["start"]=lesson.ruleStart;
          rules[lesson.ruleIdx]["end"]=lesson.ruleEnd;
          rules[lesson.ruleIdx]["day"]=lesson.ruleDay;
          rules[lesson.ruleIdx]["cnt"]=lesson.ruleCnt;
          rules[lesson.ruleIdx]["instructor"]=lesson.instructor;
          rules[lesson.ruleIdx]["park"]=lesson.park;
        });//_d(rules.length);

        if(sizeof(rules)==0){
          return true;
        }_d(rules);
        //檢查每個條件
        return checkLessonsInDays(rules);
      }//checkRuleLessons

      function orderInstructors(){

        var instructor = $('#instructor').val();//_d(instructor);

        if(instructor.length==1 && instructor[0]=='any'){//_d('Init. Inst. priority.');
          var priorities = getInstructorPriority();//_d(priorities);
          $("[schedule]").each(function(){//_d($(this).attr('schedule'));
            var _thisInstructor = regexpInstructor.exec($(this).attr('schedule'));//_d(_thisInstructor[1]);
            $(this).css('z-index', priorities[_thisInstructor[1]]);
          });//_d(_distinctInstructors);
        }

        $.each(instructor, function(i,name){
          if(typeof instructorOrders[name]==="undefined"){
            instructorOrders[name] = instructorZindex;
            instructorZindex += 1;
          }
        });//_a(instructorOrders);

        $.each(instructorOrders, function(name,zidx){//移除沒有選擇的教練
          var idx = instructor.indexOf(name);
          if(idx===-1){
            delete instructorOrders[name];
          }
        });//_d(instructorOrders);

        $.each(instructorOrders, function(name,zidx){//更改選擇較練的順序
          $("[schedule*='i="+name+"']").css('z-index', zidx);
        });

        
      }

      function showLessons(){
        var park = $('#park').val();//_d(park);
        var instructor = $('#instructor').val();//_d(instructor);

        orderInstructors();

        $("[schedule]").hide();
        if(park=='any' && instructor=='any'){//_d('1');
          $("[schedule]").show();
          $(".parkDisp").show();
        }else if(park=='any' && instructor!='any'){//_d('2');
          $.each(instructor, function(i,name){
            $("[schedule*='i="+name+",']").show();
          });
          $(".parkDisp").show();
        }else if(park!='any' && instructor=='any'){//_d('3');
          $("[schedule*='p="+park+",']").show(); 
          $(".parkDisp").hide();
        }else if(park!='any' && instructor!='any'){//_d('4');
          $.each(instructor, function(i,name){
            $("[schedule*='p="+park+",i="+name+",']").show();
          });
          $(".parkDisp").hide();
        }else{
          alert('Select error!!');
        }

      }//showLessons

      function booking(){
        //TODO 檢查訂課條件
        var order = '';
        $(".booked").each(function(){//_d(schedule);
          order += $(this).attr('schedule') + '##';
        });//_d(order);
        $("#order").val(order);

        $("#orderForm").submit();
      }

//Payment
//orderPrepaid: student changed
function calculateOrder(orderPrepaid, specialDiscount){
    var payment = {"lessons":[], "price":0, "prepaid":0, "discount":0, "paid":0, "payment":0, "currency":'', "exchangeRate":0, "insurance":''};
    var studentCnt = {};
    var studentNum = 0;
    var allDayNote = '';
    var noLevelFeePark = ['doraemon','sunac','perisher','queenstown','wanaka'];
    $(".lesson").each(function(){
        var lesson = {};
        lesson.idx = $(this).attr('id');
        lesson.sidx = $(this).attr('sidx');
        lesson.date = $(this).attr('date');
        lesson.slot = $(this).attr('slot');
        lesson.instructor = $(this).attr('instructor');
        lesson.park = $(this).attr('park');
        lesson.expertise = $(this).attr('expertise');
        lesson.ruleId = $(this).attr('ruleId');
        lesson.students = $(this).val();
        //_d(lesson.instructor+'@'+lesson.park+'#'+lesson.students);

        studentNum += parseInt(lesson.students);
        lesson.fee = parkInfo[lesson.park].base + 
                        lesson.students*parkInfo[lesson.park].unit +
                        lesson.students*parkInfo[lesson.park].insurance;
        _d('p-base:'+parkInfo[lesson.park].base+', p_unit:'+parkInfo[lesson.park].unit+', p_insur:'+parkInfo[lesson.park].insurance);
        //_d('lesson_fee:'+lesson.fee);

        if(noLevelFeePark.indexOf(lesson.park) === -1){//教練加級費
          const lessonRate = {'1h':1, '2h':1, '2.5h':1.5, '3h':2, '4h':3, '5h':3};
          lesson.fee += instructorInfo[lesson.instructor].levelFee * lessonRate[`${parkInfo[lesson.park].courseHours}h`];
          _d(`${lesson.instructor} lesson fee = ${instructorInfo[lesson.instructor].levelFee} * ${lessonRate[`${parkInfo[lesson.park].courseHours}h`]}`);
        }

        if(parkInfo[lesson.park].courseHours!=2){
          allDayNote = '<span style="color:red; font-size:10px;">請注意：' + parkInfo[lesson.park].cname + '雪場<b>一堂課為' + parkInfo[lesson.park].courseHours + '小時</b>。</span>';
        }
        if(lesson.park=='doraemon'){
          allDayNote += '<span style="color:red; font-size:10px;">遇假日需先預約好場地再預約課程。</span>';
        }

        _d(lesson.idx+':'+lesson.instructor+'@'+lesson.park+'#'+lesson.students+'='+lesson.fee);
        //_d(specialDiscount);

        $('#fee'+lesson.idx).html('<p class="price">'+lesson.fee.format()+' <small class="badge badge-primary">'+parkInfo[lesson.park].currency+'</small></p>');
        payment.price += lesson.fee;
        if(orderPrepaid==0){
          payment.prepaid += parkInfo[lesson.park].deposit;
        }else{
          payment.prepaid = orderPrepaid;
        }
        payment.currency = parkInfo[lesson.park].currency;
        payment.exchangeRate = exchangeRate[parkInfo[lesson.park].currency];
        payment.insurance = parkInfo[lesson.park].insurance;
        //計算學生數用
        if(typeof studentCnt[lesson.students]==="undefined"){
            studentCnt[lesson.students] = 0;
        }else{
            studentCnt[lesson.students] ++;
        }
        payment.lessons.push(lesson);
    });//_d(studentCnt);

    $.each(studentCnt, function(cnt, amount){//_d('amount='+amount);
        payment.discount += cnt*amount*payment.insurance;//{1: 3, 2: 0, 3: 0}
    });

    payment.insurance = studentNum * payment.insurance;
    if(orderPrepaid==0){
      payment.prepaid += payment.insurance - payment.discount;//_d('insurance='+payment.insurance+', prepaid='+payment.prepaid+', studentNum='+studentNum);
    }else{
      payment.prepaid = orderPrepaid;
    }_d(payment.prepaid);

    payment.paid = payment.prepaid * payment.exchangeRate;
    payment.payment = payment.price - payment.discount - payment.prepaid;
    _d(specialDiscount);
    if(specialDiscount!=0 && specialDiscount!=undefined){
      payment.discount = payment.discount - specialDiscount;
      payment.payment = payment.payment + specialDiscount;
    }

    $('#price').html('<p class="num col s8 m12">'+payment.price.format()+' <small class="badge badge-primary">'+payment.currency+'</small><br>'+allDayNote+'</p>');       
    $('#prepaid').html('<p class="num col s8 m12">'+payment.prepaid.format()+' <small class="badge badge-primary">'+payment.currency+'</small></p> ');
    $('#discount').html('<p class="num col s8 m12">'+payment.discount.format()+' <small class="badge badge-primary">'+payment.currency+'</small></p> ');
    $('#exchangeRate').html('(匯率: '+payment.exchangeRate+')');
    $('#paid').html('<p class="num col s8 m12">'+payment.paid.format()+' <small class="badge badge-primary">NTD</small></p> ');
    $('#payment').html('<p class="num col s8 m12">'+payment.payment.format()+' <small class="badge badge-primary">'+payment.currency+'</small></p> ');

    var paymentJson = JSON.stringify(payment);//_d(paymentJson);
    $('#paymentData').val(paymentJson);
}


function createFollow(data){
  $.ajax({
    url: "follow.php?action=create",                 
    type: "POST",
    data: data
  }).done(function(resp){//_d(resp);
    resp = $.parseJSON(resp);
    alert(resp.message);
  }).fail(function(resp){
    alert('連線異常，請重新操作！');
  });
}

function follow(e){
  e.preventDefault();

  var date = $(this).attr('date');
  var expertise = $(this).attr('expertise');
  var park = $(this).attr('park');
  var instructors = $('#instructor').val();

  if(park=='any' && instructors.length==1 && instructors[0]=='any'){
    alert('請至少選擇ㄧ雪場或教練！');
    return false;
  }

  if(instructors[0]=='any' && instructors.length!=1){
    instructors.shift();
  }
          
  var instStr = '';
  $.each(instructors, function(i,v){
    if(v=='any') {
      instStr += '不限' + ', ';
    }else{
      instStr += instructorInfo[v]['cname'] + ', ';
    }
  });instStr = instStr.slice(0, -2);//_d(instStr);

  var follow = confirm("確定是否追蹤此條件開課通知？\n" + 
    "日期：" + date + " 前後三天\n" +
    "課程：" + ((expertise=='sb') ? '單板' : '雙板') + "\n" +
    "雪場：" + ((park=='any') ? '不限' : parkInfo[park]['cname']) + "\n" +
    "教練：" + instStr + ".\n\n" + 
    "確定後系統將每週ㄧ、四發送追蹤通知信，\n您可以隨時至 [帳號] -> [課程追蹤] 取消設定。"
    );

  if(follow){
    createFollow({
      'date': date, 'expertise': expertise, 
      'park': park, 'instructors': instructors
    });
  }

}

