//取cookies函数
function getCookie(name){
  var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
  if(arr != null) return unescape(arr[2]); return null;
}

function addFavor(){
  var thisTitle='162100 - 最好的网址导航 - 一路爱意浓浓';
  var thisUrl='http://www.162100.com/';
  if(document.all)
    window.external.AddFavorite(thisUrl,thisTitle);
  else if(window.sidebar)
    window.sidebar.addPanel(thisTitle,thisUrl,"");
  return false;
}

function SetHome(obj,vrl){
  try{
    obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl);
  }catch(e){
    if(window.netscape){
      try{
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
      }catch(e){
        alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将[signed.applets.codebase_principal_support]设置为'true'"); 
      }
      var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
      prefs.setCharPref('browser.startup.homepage',vrl);
    }
  }
}

