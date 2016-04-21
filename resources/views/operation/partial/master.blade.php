<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>运营后台</title>
    <link rel="stylesheet" type="text/css" href="http://7xnzm2.com2.z0.glb.qiniucdn.com/bootstrap.min.css">
    <link href="{{url("image/LOGO.ico")}}" type="image/x-icon" rel="shortcut icon"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/jquery.min.js"></script>
    <script src="http://apps.bdimg.com/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/bootstrap.min.js"></script>
    <script src="{{url('js/vue.min.js')}}"></script>
    <script type="text/javascript" src="{{url('js/jqClock.min.js')}}"></script>
    <script src="https://cdn1.lncld.net/static/js/av-mini-0.5.4.js"></script>
    <script src="https://cdn1.lncld.net/static/js/av-core-mini-0.5.4.js"></script>
    <script src = "https://cdn.wilddog.com/js/client/current/wilddog.js" ></script>
    <script>
        AV.initialize("1uCLCKq2T7Y4jh0VxXgBOLpV", "Sh2vHA09uWO21vuvBCTL8bop");
//        AV.initialize("k1cslg2apm1r2onvapnh5elh8wiqooc5u75d23e4jiltmiyf", "zi83n282qzi93sepg6wzlx7jrk5e79ph4219snepgxfiurcv");
    </script>
</head>
<style type="text/css">
    body > .container {
        padding: 40px 0;
        font-size: 10px;
    }

    th, td {
        text-align: right;
    }

    .edit, .createItem {
        width: 80px;
        line-height: 100%;
        margin-bottom: 5px;
        margin-top: 5px;
    }

    a, a:hover, a:visited, a:active, a:link {
        text-decoration: none !important;
    }

    .refundRecord, .refundRecord:hover, .refundRecord:visited, .refundRecord:active, .refundRecord:link {
        color: red;
    }

    table {
        width: 100%;
    }

    tbody {
        border: 1px solid #f2f2f2;
    }

    p {
        margin: 0;
    }

    #operator {
        margin-right: 30px;
    }

    #requirementNumber {
        color: blue;
        font-weight: bold;
    }

    #requirement_order_id {
        color: blue;
        font-weight: bold;

    }

    #yeyeHead {
        font-family: 'Comic Sans MS', sans-serif;
    }

    .separation-row {
        height: 10px;
        border: none
    }

    .header-row {
        background-color: #f2f2f2;
        height: 40px
    }

    .body-row {
        height: 70px;
    }

    .th1 {
        width: 25%;
        text-align: center;
    }

    .th2 {
        width: 10%;
    }

    .th3 {
        width: 10%;
    }

    .th4 {
        width: 10%;
    }

    .th5 {
        width: 17%;
    }

    .th6 {
        width: 14%;
    }

    .th7 {
        width: 14%;
    }

    .title-cell {
        width: 25%;
        text-align: left
    }

    .title {
        display: inline-block;
        width: 200px;
        float: left;
        padding-left: 10px;
    }

    .price-cell {
        width: 10%;
        text-align: right
    }

    .country-cell {
        width: 10%;
    }

    .buyer-cell {
        width: 10%;
    }

    .email-cell {
        width: 17%;
    }

    .email, .mobile {
        word-break: break-all;
        display: inline-block;
        width: 170px;
    }

    .itemStatus-cell {
        width: 14%;
    }

    .edit-cell {
        width: 14%;
    }

    .placeholder {
        opacity: 0;
    }

    .thumb_url {
        width: 60px;
        height: 60px;
        padding-left: 5px;
        float: left;
    }

    .form-group {
        margin-bottom: 5px;
    }

    /*page requireDetail*/
    .thItem {
        width: 30%;
        text-align: center;
    }

    .thPrice {
        width: 10%;
        text-align: center;
    }

    .thNumber {
        width: 10%;
        text-align: center;
    }

    .thDescription {
        width: 20%;
        text-align: center;
    }

    .thStatus {
        width: 15%;
        text-align: center;
    }

    .thCreatItem {
        width: 15%;
        text-align: center;
    }

    #requireDetail .title-cell, #orderDetail .title-cell {
        width: 30%;
    }

    #requireDetail .title, #orderDetail .title {
        display: inline-block;
        width: 200px;
        word-break: break-all;
    }

    #requireDetail .thumb_url, #orderDetail .thumb_url {
        width: 60px;
        height: 60px;
    }

    #requireDetail .price-cell, #orderDetail .price-cell {
        width: 10%;
        text-align: center
    }

    #requireDetail .number-cell, #orderDetail .number-cell {
        text-align: center;
        width: 10%;
    }

    #requireDetail .description-cell, #orderDetail .description-cell {
        width: 20%;
        text-align: center;
    }

    #requireDetail .description-cell .description, #orderDetail .description-cell .description {
        word-break: break-all;
    }

    #requireDetail .status-cell, #orderDetail .status-cell {
        width: 15%;
        text-align: center;
    }

    #requireDetail .status, #orderDetail .status {
        color: red;
    }

    #requireDetail .saved, #orderDetail .saved {
        color: black;
    }

    #requireDetail .change-cell, #orderDetail .change-cell {
        width: 15%;
        text-align: center;
    }

    #requireDetail .change-cell .change, .deleteThis {
        width: 80px;
        line-height: 100%;
    }

    #requireDetail .change {
    }

    #requireDetail .buttonarea {
        width: 100%;
        text-align: center;
    }

    #orderDetail .buyerInfo p {
        padding-right: 50px
    }

    #orderDetail .innerRight {
        text-align: right
    }

    #orderDetail .innerRight a {
        margin-left: 5px;
    }

    #orderDetail .widthFixed {
        display: inline-block;
        width: 8em
    }

    #orderDetail .showRight {
        display: inline-block;
        float: right;
        color: blue
    }

    #changeBuyer {
        display: none;
        position: fixed;
        z-index: 3;
        top: 80px;
        background-color: white;
        width: 20%;
        left: 20%;
        padding: 10px 20px 20px 20px;
        border-radius: 5px;
    }

    #changeOperator {
        display: none;
        position: fixed;
        z-index: 3;
        top: 80px;
        background-color: white;
        width: 20%;
        right: 20%;
        padding: 10px 20px 20px 20px;
        border-radius: 5px;
    }

    #changeBuyer > p, #changeOperator > p {
        text-align: right;
    }

    #sendInfo {
        display: none;
        position: fixed;
        z-index: 3;
        top: 80px;
        background-color: white;
        width: 30%;
        right: 20%;
        padding: 10px 20px;
    }

    #express {
        width: 45%;
        display: inline-block;
        margin-right: 5%;
    }

    #otherExpress {
        width: 45%;
        display: none;
    }

    #refund {
        display: none;
        position: fixed;
        z-index: 3;
        top: 80px;
        background-color: white;
        width: 30%;
        right: 20%;
        border-radius: 5px;
        padding: 10px 20px;
    }

    #refundPart {
        display: none;
        position: fixed;
        z-index: 3;
        top: 80px;
        background-color: white;
        width: 30%;
        right: 20%;
        border-radius: 5px;
        padding: 10px 20px;
    }

    /*page requirementDetail 下的editRequireDetail    */
    #background {
        position: fixed;
        top: 0;
        left: 0;
        background-color: black;
        opacity: 0.5;
        display: none;
        z-index: 2;
    }

    #editRequireDetail {
        display: none;
        position: fixed;
        z-index: 3;
        top: 60px;
        background-color: white;
        width: 50%;
        left: 25%;
    }

    #editRequireDetail .closed {
        margin: 5px;
        float: right;
    }

    #editRequireDetail .save {
        margin: 5px;
        float: right;
    }

    #editRequireDetail form {
        width: 90%;
        margin: 30px auto;
    }

    #editRequireDetail .imageArea {
        max-height: 120px;
        overflow-y: auto;
    }

    #editRequireDetail .showImageEx {
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        margin: 5px;
        float: left;
        display: none;
    }

    #editRequireDetail .showImage {
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        margin: 5px;
        float: left;
    }

    #editRequireDetail .showImage img {
        height: 100%;
        width: 100%;
    }

    #editRequireDetail .addImage {
        margin: 5px;
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        float: left;
        overflow: hidden;
        position: relative;
        text-align: center;
    }

    #editRequireDetail .deleteImage {
        position: relative;
        top: -110px;
        left: 80px;
        color: gray;
        font-size: 30px;
        font-weight: bolder;
    }

    #editRequireDetail .chosefiles {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: 4;
    }

    #editRequireDetail .addImage .addicon {
        font-size: 70px;
        padding: 0;
        margin-top: 15px;
        margin-bottom: 15px;
        height: 70px;
        font-weight: bolder;
        color: gray;
    }

    #editRequireDetail .savearea {
        text-align: center;
    }

    #myCarousel {
        position: fixed;
        width: 50%;
        left: 25%;
        display: none;
        top: 60px;
        z-index: 4;
        background-color: rgba(0, 0, 0, 0.5);
    }

    #myCarousel .item img {
        margin: 52px auto;
        height: 500px;
        width: 500px;
    }

    /*requirementDetail 之后的divideOrder*/
    #divideOrder .detail, #divideOrder .detailEx, #divideOrder .detailHeader {
        border: 1px solid rgb(220, 220, 220);
        margin-bottom: 10px;
        border-radius: 4px;
    }

    #divideOrder .title-cell {
        width: 30%;
    }

    #divideOrder .title {
        display: inline-block;
        width: 200px;
        word-break: break-all;
    }

    #divideOrder .thumb_url {
        width: 60px;
        height: 60px;
    }

    #divideOrder .price-cell {
        width: 10%;
        text-align: center
    }

    #divideOrder .number-cell {
        text-align: center;
        width: 10%;
    }

    #divideOrder .description-cell {
        width: 20%;
        text-align: center;
    }

    #divideOrder .description-cell .description {
        word-break: break-all;
    }

    #divideOrder .status-cell {
        width: 15%;
        text-align: center;
    }

    #divideOrder .change-cell {
        width: 15%;
        text-align: center;
    }

    #divideOrder .change-cell .change, .deleteThis {
        width: 80px;
        line-height: 100%;
    }

    #divideOrder .saveOrder, .deleteMainOrder {
        margin-left: 1em;
    }

    #divideOrder .editOrder, .deleteChildOrder {
        display: none;
        margin-left: 1em;
    }

    #divideOrder .change {
        color: white;
    }

    #divideOrder .buttonarea {
        width: 100%;
        text-align: center;
    }

    #divideOrder .selectedCountry {
        /*position: absolute;*/
        /*top: -15px;*/
        width: 100px
    }

    /*是否确认领取    */
    #collectOrNot {
        position: fixed;
        top: 0;
        height: 100%;
        width: 200%;
        margin-left: -25%;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.5)
    }

    #collectOrNot div {
        position: fixed;
        top: 48%;
        left: 43%
    }

    #collectOrNot a:nth-child(1) {
        width: 80px
    }

    #collectOrNot a:nth-child(2) {
        width: 80px;
        margin-left: 20px
    }

    /*退款记录页样式*/
    #refundRecord {
        display: none;
        position: fixed;
        top: 100px;
        left: 35%;
        z-index: 3;
        width: 30%;
        overflow-y: auto;
        padding: 10px;
        background-color: rgb(255, 255, 255);
        border-radius: 5px;
    }

    #refundRecord .suborder_numberOuter {
        color: blue;
        margin: 10px 0 0 0;
    }

    #refundRecord p:last-child {
        text-align: center;
    }

    #refundRecord hr {
        margin: 10px 0;
    }

    /*买手管理列表页样式*/
    #seller .sellerOverview th, #buyer .buyerOverview th {
        text-align: center;
    }

    #seller .sellerOverview .right, #buyer .buyerOverview .right {
        text-align: right;
    }

    #seller .info-cell, #buyer .info-cell {
        width: 25%;
        text-align: left
    }

    #seller .country-cell, #buyer .country-cell {
        width: 10%;
        text-align: center;
    }

    #seller .email-cell, #buyer .email-cell {
        width: 20%;
    }

    #seller .sellerStatus-cell, #buyer .sellerStatus-cell {
        width: 10%;
    }

    #seller .orders-cell, #buyer .orders-cell {
        width: 20%;
    }

    #seller .edit-cell, #buyer .edit-cell {
        width: 15%;
    }

    /*买手管理详情页样式*/
    #sellerDetail td, #buyerDetail td, #staffDetail td {
        width: 28%;
        text-align: left;
    }

    #sellerDetail td:first-child, #buyerDetail td, #staffDetail td:first-child {
        width: 16%;
    }

    #sellerDetail img, #buyerDetail img, #staffDetail img {
        width: 120px;
        height: 120px;
    }

    #staffDetail select {
        height: 25px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    /*资金管理打款管理页样式*/
    #remittanceHeader .th1 {
        width: 20%;
        text-align: center;
    }

    #remittanceHeader .th2 {
        width: 8%;
    }

    #remittanceHeader .th3 {
        width: 20%;
    }

    #remittanceHeader .th4 {
        width: 7%;
    }

    #remittanceHeader .th5 {
        width: 10%;
    }

    #remittanceHeader .th6 {
        width: 15%;
    }

    #remittanceHeader .th7 {
        width: 20%;
    }

    #remittanceHeader .title-cell {
        width: 25%;
        text-align: left
    }

    #remittanceHeader .title {
        display: inline-block;
        width: 200px;
        float: left;
        padding-left: 10px;
    }

    #remittanceHeader .seller-cell {
        width: 8%;
        text-align: right
    }

    #remittanceHeader .buyer-cell {
        width: 15%;
    }

    #remittanceHeader .order-status-cell {
        width: 7%;
    }

    #remittanceHeader .operator-cell {
        width: 10%;
    }

    #remittanceHeader .email, .mobile {
        word-break: break-all;
        display: inline-block;
        width: 170px;
    }

    #remittanceHeader .remittance-cell {
        width: 15%;
    }

    #remittanceHeader .edit-cell {
        width: 20%;
    }

    #sureToRemittance .remittanceInfo hr {
        margin: 10px 0;
    }

    /*员工管理页样式*/
    #staff th, #staff td {
        text-align: center;
    }

    .alert .email, .alert .mobile {
        width: 500px;
        word-break: normal;
    }

    /*活动管理页样式*/
    #activities th, #activities td {
        text-align: center;
    }

    #activities th:nth-child(1) {
        width: 30%;
    }

    #activities th:nth-child(2) {
        width: 30%;
    }

    #activities th:nth-child(3) {
        width: 10%;
    }

    #activities th:nth-child(4) {
        width: 10%;
    }

    #activities th:nth-child(5) {
        width: 20%;
    }

    /*编辑活动详情页样式*/
    #activityDetail .showImageEx {
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        margin: 5px;
        float: left;
        display: none;
    }

    #activityDetail .showImage, #itemEditing .showImage {
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        margin: 5px;
        float: left;
    }

    #activityDetail .showImage img, #itemEditing .showImage img {
        height: 100%;
        width: 100%;
    }

    #activityDetail .addImage, #itemEditing .addImage {
        margin: 5px;
        width: 100px;
        height: 100px;
        background-color: #f2f2f2;
        float: left;
        overflow: hidden;
        position: relative;
        text-align: center;
    }

    #activityDetail .deleteImage, #itemEditing .deleteImage {
        position: relative;
        top: -110px;
        left: 80px;
        color: gray;
        font-size: 30px;
        font-weight: bolder;
    }

    #activityDetail .chosefiles, #itemEditing .chosefiles {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: 4;
        top: 0;
        left: 0;
    }

    #activityDetail .addImage .addicon, #itemEditing .addImage .addicon {
        font-size: 70px;
        padding: 0;
        margin-top: 15px;
        margin-bottom: 15px;
        height: 70px;
        font-weight: bolder;
        color: gray;
    }

    #activityDetail .right {
        display: inline-block;
        float: right;
    }

    #itemList section {
        border: 1px solid gray;
    }

    #itemEx {
        display: none;
    }

    .navbar-fixed-top .dropdown-menu {
        min-width: 86px;
        width: 96px;
        text-align: center
    }

    .navbar-fixed-top .dropdown-menu a {
        padding: 3px 0;
    }

    #tagsList [class^="col-sm-"] {
        padding: 0;
    }

    #tagsList .col-sm-4 {
        text-align: right;
    }

    .dropdown-menu {
        min-width: 70px;
        text-align: center;
        padding: 5px 0;
    }

    .dropdown span {
        cursor: pointer;
    }

    .row{
        margin-left: 0;
        margin-right: 0;
    }


    #chat .chat_box{
        position: fixed;
        z-index: 9999;
        right: 20px;
        bottom: 0;
        width: 250px;
    }
    #chat .chat_body{
        display: none;
        background: white;
        height: 400px;
        padding: 5px 0;
        overflow: auto;
    }

    #chat .chat_head,.msg_head{
        background: #f39c12;
        color: white;
        padding: 15px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 5px 5px 0 0;
    }

    #chat .msg_box{
        position: fixed;
        display: none;
        z-index: 9999;
        bottom: -5px;
        width: 250px;
        background: white;
        border-radius: 5px 5px 0 0;
    }

    #chat .msg_head{
        background: #3498db;
    }

    #chat .msg_body{
        background: white;
        height: 200px;
        font-size: 12px;
        padding: 15px;
        overflow: auto;
        overflow-x: hidden;
    }
    #chat .msg_input{
        width: 100%;
        border: 1px solid white;
        border-top: 1px solid #DDDDDD;
        -webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
        -moz-box-sizing: border-box;    /* Firefox, other Gecko */
        box-sizing: border-box;
    }

    #chat .close{
        float: right;
        cursor: pointer;
    }
    #chat .minimize{
        float: right;
        cursor: pointer;
        padding-right: 5px;

    }

    #chat .user{
        position: relative;
        padding: 10px 0 10px 30px;
    }
    #chat .user:hover{
        background: #f8f8f8;
        cursor: pointer;

    }
    #chat .user:before{
        content: '';
        position: absolute;
        background: #2ecc71;
        height: 10px;
        width: 10px;
        left: 10px;
        top: 15px;
        border-radius: 6px;
    }

    #chat .msg_a{
        position: relative;
        background: #FDE4CE;
        padding: 10px;
        min-height: 10px;
        margin-bottom: 5px;
        margin-right: 10px;
        border-radius: 5px;
    }
    #chat .msg_a:before{
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border: 10px solid;
        border-color: transparent #FDE4CE transparent transparent;
        left: -20px;
        top: 7px;
    }


    #chat .msg_b{
        background:#EEF2E7;
        padding: 10px;
        min-height: 15px;
        margin-bottom: 5px;
        position: relative;
        margin-left: 10px;
        border-radius: 5px;
    }
    #chat .msg_b:after{
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border: 10px solid;
        border-color: transparent transparent transparent #EEF2E7;
        right: -20px;
        top: 7px;
    }
</style>
<script>
    String.prototype.subStrByByte = function(limit) {
        var newStr="";
        var len=0;
        for(var i=0; i<this.length; i++){
            if((/[^\x00-\xff]/g).test(this[i])){
                len +=2;
            }else{
                len +=1;
            }
            if(len>limit){
                newStr=this.substr(0,i);
                return newStr;
            }
        }
        return this.toString();
    };

    $(document).ready(function () {
        var customtimestamp = new Date().getTime();
        var am = customtimestamp - 12 * 3600000;
        var en = customtimestamp - 7 * 3600000;
        var au = customtimestamp + 2 * 3600000;
        var jp = customtimestamp + 3600000;
        $("div#clock").clock({"format": "24", "calendar": "false"});
        $("span#clock2").clock({"timestamp": am, "calendar": "false", "seconds": "false"});
        $("span#clock3").clock({"timestamp": en, "calendar": "false", "seconds": "false"});
        $("span#clock4").clock({"timestamp": au, "calendar": "false", "seconds": "false"});
        $("span#clock5").clock({"timestamp": jp, "calendar": "false", "seconds": "false"});

        $.ajaxSetup({
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
            dataType: 'json',
            error: function (request, errorType, errorMessage) {
                alert("error:" + errorType + ";  message:" + errorMessage);
            }
        });

        var checkRequireDone = [];
        window.myRequire = function(url, afterRequire, afterLoad) {
            var __markThis = {mark: false};
            checkRequireDone.push(__markThis);
            $.ajax({
                url: url,
                dataType: "html",
                success: function(res) {
                    __markThis.mark = true;
                    console.log('loaded');
                    var component = res.split("</style>");
                    if (component.length > 1) {
                        $("head").append(component[0] + "</style>");
                        $("body").append(component[1]);
                    } else {
                        $("body").append(component[0]);
                    }
//                    if (typeof afterLoad == "function") {                   //组件嵌套时使用,暂时先不用
//                        afterLoad();
//                    }
                    if (checkRequireDone.length == checkRequireDone.filter(function(index) {return index.mark}).length) {
                        afterRequire()
                    }
                }
            });
        };

    });
</script>
<body>
@section('header')
    @include('operation.partial.header');
@show

<div class="container">
    @yield('content')

</div>

<div id="vueScope">
    <chat-modal></chat-modal>
    <template id="chat-template">
        <div id="chat">
            <div class="chat_box">
                <div class="chat_head" @click="toggleList"> 待处理 <span class="badge" style="background-color: red">@{{ getObjectLength(users) }}</span></div>
                <div class="chat_body">
                    <div class="user" v-for="user in users | orderBy 'updated_at' -1 order" @click="showMsg(user)">
                        <img :src="user.info.headimgurl" style="width: 20px">
                        @{{ user.info.nickname }}
                        <span class="badge" style="background-color: red">@{{getObjectLength(user.messages)}}</span>
                        <small style="color: #aaa">@{{user.updated_at.substr(10,20)}}</small>
                    </div>
                </div>
            </div>
            <div class="msg_box" style="right:290px">
                <div class="msg_head" @click.stop="toggleMsg">@{{chatChecking.info.nickname}}(@{{ chatChecking.info.province + chatChecking.info.city + (chatChecking.info.mobile === undefined ? "" : chatChecking.info.mobile) }})
                    <div class="close" @click="closeMsg">&times;</div>
                </div>
                <div class="msg_wrap">
                    <div class="msg_body">
                        <div class="msg_a" v-for="chatMessage in chatChecking.messages">@{{chatMessage.message}}</div>
                    </div>
                    <div class="msg_footer" style="text-align: center; padding-bottom: 15px">
                        <button @click="closeMsg" class="btn btn-success" style="margin-right: 10px">知道了</button>
                        <button @click="toDealIt(chatChecking)" class="btn btn-primary" style="margin-left: 10px">去处理</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    var ref = new Wilddog("https://buypal.wilddogio.com/");
    var userRef = ref.child('users');


    Vue.component('chat-modal', {
        template: '#chat-template',
        data: function() {
            return {
                users: [],
                chatChecking: {}
            }
        },
        methods: {
            toggleList: function() {
                $('.chat_body').slideToggle('slow');
            },
            toggleMsg: function() {
                $('.msg_wrap').slideToggle('slow');
            },
            closeMsg: function() {
                $('.msg_box').hide();
            },
            showMsg: function(chat) {
                $('.msg_wrap').show();
                $('.msg_box').show();
                this.chatChecking = chat;
            },
            toDealIt: function(chat) {
                userRef.child(chat.open_id).remove();
                this.users.$remove(chat);
                $('.msg_box').hide();
            },
            getObjectLength: function(ob) {
                return Object.keys(ob).length;
            }
        },
        created: function() {
            var that = this;

            userRef.on('child_added', function(data) {
                var user = data.val();
                user.open_id = data.key();
                that.users.push(user);
            });

            userRef.on('child_changed', function(data) {
                that.users.map(function(index, i) {
                    if (index.open_id == data.key()) {
                        var newData = data.val();
                        newData.open_id = data.key();
                        that.users.splice(i, 1, newData);
                        if (that.chatChecking.open_id === data.key()) {
                            that.chatChecking = newData;
                        }
                    }
                })
            });

            userRef.on('child_removed', function(data) {
                that.users.map(function(index, i){
                    if (index.open_id == data.key()) {
                        that.users.splice(i, 1);
                    }
                });
            })
        }
    });
    new Vue({
        el: '#vueScope'
    });
</script>
<script type="text/javascript" src="/js/operator/deleteMemo.js"></script>

</body>
</html>