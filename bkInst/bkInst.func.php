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
      var regexpRule = /x=(\d+),d=(.+),s=([1-9]),p=([a-z]+),i=([a-z0-9]+),e=(.+),ri=(.+),rs=(.+),re=(.+),rc=(\d+)d(\d+)c/i;
      var regexpCust = /x=(\d+),d=(.+),s=([1-9]),p=([a-z]+),i=([a-z0-9]+),e=(.+)/i;
      var regexpDisable = /x=(\d+),d=(.+),s=([1-9]),p=(.*),i=([a-z0-9]+),e=(disable)/i;
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


      function showPark(){
        var park = $('#park').val();//_d(park);
        if(park=='any'){
          $("[schedule]").show();  
        }else if(park=='group'){
          $("[schedule]").hide();
          $("[schedule*='s=9,']").show();
        }else{
          $("[schedule]").hide();
          $("[schedule*='p="+park+",']").show();
        }
      }//showLessons

function getOrderInfo(oidx){
  var endpoint = "getOrderInfo.php?token=<?=rand(111,9999)?>&oidx=" + oidx + '&time=' + Math.random();_d(endpoint);
  $.ajax({
    url: endpoint,
    type: "GET",
    success: function(resp){//_d(resp);
      $('#orderInfo').html(resp);
    },
    fail: function(resp){_d('get fail');
    }
  });
}

function getGroupOrderInfo(gidx){
  var endpoint = "getGroupOrderInfo.php?token=<?=rand(111,9999)?>&gidx=" + gidx + '&time=' + Math.random();_d(endpoint);
  $.ajax({
    url: endpoint,
    type: "GET",
    success: function(resp){//_d(resp);
      $('#orderInfo').html(resp);
    },
    fail: function(resp){_d('get fail');
    }
  });
}