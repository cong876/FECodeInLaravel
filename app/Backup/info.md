#耶烨共享 vendor文件及其说明

---

## Backup_AccessToken.php

+ 该文件上线前覆盖到vendor/overture/wechat/src/Wechat下；
+ 覆盖前将前缀'Backup_'删除；
+ 该文件保证微信的AccessToken能够被正确地解析和保存。

---

## Backup_ApiRequestor.php

+ 该文件上线前覆盖到vendor/pingplusplus/lib下；
+ 覆盖前将前缀'Backup_'删除；
+ 该文件保证cert签名审核能够被hhvm争取解析，并在服务器上正确执行支付逻辑。

