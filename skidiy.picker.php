//---------------[1]sidx [2]date [3]slot [4]park [5]instructor [6]expertise [7]ruleIdx [8]start [9]end [10]day [11]slot
      var regexpRule = /x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z]+),e=(.+),ri=(\d+),rs=(.+),re=(.+),rc=(\d+)d(\d+)c/i;
      var regexpCust = /x=(\d+),d=(.+),s=([1-4]),p=([a-z]+),i=([a-z]+),e=(.+)/i;
      var instructorOrders = {};
      var instructorZindex = 1;
      var ruleColor = ['#900000','#5ab2ec','#ffc443','#ff5a6e','#cdb38b','#55c2b7','#ffc0cb','#ccccff','#a4a527','#c31aff'];
      var bookedLessons = 0;

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
          park: arr[3],
          slot: arr[4],
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

      function checkLessonsInDays(data){//_d(data);
        for (var ruleIdx in data){//_d(ruleIdx);
          var dates = data[ruleIdx].dates;//_d(dates);
          var day = data[ruleIdx].day;
          var cnt = data[ruleIdx].cnt;
          var start = data[ruleIdx].start;
          var end = data[ruleIdx].end;
          var dateTS = [];
          var dateTScnt = {};
          var dateTSday = {};

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
            var notify = start + '～' + end + '需選課' + cnt + '堂以上才能開課成功喔～' 
                 + '目前還差' + (cnt-totalCnt) + '堂！';
            $("#notify").html(notify);
          }else{
            $("#notify").html('');
          }
        }//for rules
        
      }//checkLessonsInDays

      function showSummary(){
        bookedLessons = 0;//重置
        $(".booked").each(function(){
          bookedLessons += 1;
        });
        if(bookedLessons>=1){
          $('#summary').html('您目前選了'+bookedLessons+'堂課。');
        }else{
          $('#summary').html('請點選下列日期與時段訂課。');
        }
      }

      function checkRuleLessons(){
        var rules = {};

        //收集所有訂課的條件
        $(".booked").each(function(){
          //x=123,d=2018-09-30,p=naeba,s=1,i=amber,e=sb,ri=124,rs=2018-09-30,re=2018-10-10,rc=3d2s
          var schedule = $(this).attr('schedule');_d(schedule);
          var lessonArr = regexpRule.exec(schedule);_d(lessonArr);
          
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
        });//_d(rules.length);

        if(sizeof(rules)==0){
          $("#notify").html('');
          return false;
        }//_d(rules.length);
        //檢查每個條件
        checkLessonsInDays(rules);
      }//checkRuleLessons

      function orderInstructors(){
        var instructor = $('#instructor').val();//_d(instructor);
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

        }else if(park=='any' && instructor!='any'){//_d('2');
          $.each(instructor, function(i,name){
            $("[schedule*='i="+name+"']").show();
          });

        }else if(park!='any' && instructor=='any'){//_d('3');
          $("[schedule*='p="+park+",']").show(); 

        }else if(park!='any' && instructor!='any'){//_d('4');
          $.each(instructor, function(i,name){
            $("[schedule*='p="+park+",i="+name+"']").show();
          });
        }else{
          alert('Select error!!');
        }
      }//showLessons

      function booking(){
        if( bookedLessons<1 || $("#notify").html()!='' ){
          $("#notify").html('您尚未選課完成，無法訂課！');
          return false;
        }
        //TODO 檢查訂課條件
        var order = '';
        $(".booked").each(function(){//_d(schedule);
          order += $(this).attr('schedule') + '##';
        });_d(order);
        $("#order").val(order);

        $("#orderForm").submit();
      }