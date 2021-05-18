"use strict";var socialLogin=angular.module("socialLogin",[]);socialLogin.provider("social",function(){var e,o,t,i;return{setFbKey:function(t){e=t.appId,o=t.apiVersion;var i,n=document,c=n.getElementsByTagName("script")[0];(i=n.createElement("script")).id="facebook-jssdk",i.async=!0,i.src="//connect.facebook.net/en_US/sdk.js",i.onload=function(){FB.init({appId:e,status:!0,cookie:!0,xfbml:!0,version:o})},c.parentNode.insertBefore(i,c)},setGoogleKey:function(e){t=e;var o,i=document,n=i.getElementsByTagName("script")[0];(o=i.createElement("script")).async=!0,o.src="//apis.google.com/js/platform.js",o.onload=function(){var o={client_id:e,scope:"email"};gapi.load("auth2",function(){gapi.auth2.init(o)})},n.parentNode.insertBefore(o,n)},setLinkedInKey:function(e){i=e;var o,t=document,n=t.getElementsByTagName("script")[0];(o=t.createElement("script")).async=!1,o.src="//platform.linkedin.com/in.js",o.text=("api_key: "+i).replace('"',""),n.parentNode.insertBefore(o,n)},$get:function(){return{fbKey:e,googleKey:t,linkedInKey:i,fbApiV:o}}}}),socialLogin.factory("socialLoginService",["$window","$rootScope",function(e,o){return{logout:function(){switch(console.log("social service logout "),e.localStorage.getItem("_login_provider")){case"google":var t=document.getElementById("gSignout");void 0!==t&&null!=t&&t.remove();var i,n=document,c=n.getElementsByTagName("script")[0];(i=n.createElement("script")).src="https://accounts.google.com/Logout",i.type="text/html",i.id="gSignout",e.localStorage.removeItem("_login_provider"),o.$broadcast("event:social-sign-out-success","success"),c.parentNode.insertBefore(i,c);break;case"linkedIn":IN.User.logout(function(){e.localStorage.removeItem("_login_provider"),o.$broadcast("event:social-sign-out-success","success")},{});break;case"facebook":FB.logout(function(t){e.localStorage.removeItem("_login_provider"),o.$broadcast("event:social-sign-out-success","success")})}},setProvider:function(o){e.localStorage.setItem("_login_provider",o)}}}]),socialLogin.directive("linkedIn",["$rootScope","social","socialLoginService","$window",function(e,o,t,i){return{restrict:"EA",scope:{},link:function(o,i,n){i.on("click",function(){IN.User.authorize(function(){IN.API.Raw("/people/~:(id,first-name,last-name,email-address,picture-url)").result(function(o){t.setProvider("linkedIn");var i={name:o.firstName+" "+o.lastName,email:o.emailAddress,uid:o.id,provider:"linkedIN",imageUrl:o.pictureUrl};e.$broadcast("event:social-sign-in-success",i)})})})}}}]),socialLogin.directive("gLogin",["$rootScope","social","socialLoginService",function(e,o,t){return console.log("inside google directive"),{restrict:"EA",scope:{},replace:!0,link:function(o,i,n){i.on("click",function(){var i=function(){var e=o.gauth.currentUser.get(),t=e.getBasicProfile(),i=e.getAuthResponse().id_token,n=e.getAuthResponse().access_token;return console.log("basic profile of google user "+JSON.stringify(t)),{token:n,idToken:i,name:t.getName(),email:t.getEmail(),uid:t.getId(),provider:"GOOGLE",imageUrl:t.getImageUrl()}};void 0===o.gauth&&(o.gauth=gapi.auth2.getAuthInstance()),o.gauth.isSignedIn.get()?(t.setProvider("google"),e.$broadcast("event:social-sign-in-success",i())):o.gauth.signIn().then(function(o){console.log(o),t.setProvider("google"),e.$broadcast("event:social-sign-in-success",i())},function(e){console.log(e)})})}}}]),socialLogin.directive("fbLogin",["$rootScope","social","socialLoginService","$q",function(e,o,t,i){return console.log("inside fbLogin directive"),{restrict:"EA",scope:{},replace:!0,link:function(o,n,c){n.on("click",function(){var o=function(){var e=i.defer();return FB.api("/me?fields=name,email,picture",function(o){!o||o.error?e.reject("Error occured while fetching user details."):e.resolve({name:o.name,email:o.email,uid:o.id,provider:"FACEBOOK",imageUrl:o.picture.data.url})}),e.promise};FB.getLoginStatus(function(i){"connected"===i.status?o().then(function(o){console.log(o),o.token=i.authResponse.accessToken,t.setProvider("facebook"),e.$broadcast("event:social-sign-in-success",o)}):FB.login(function(i){"connected"===i.status&&o().then(function(o){o.token=i.authResponse.accessToken,t.setProvider("facebook"),e.$broadcast("event:social-sign-in-success",o)})},{scope:"email",auth_type:"rerequest"})})})}}}]);