use openemr;

UPDATE globals
     SET gl_value = '1'
     WHERE gl_name = 'portal_onsite_two_enable';

INSERT INTO `insurance_data` VALUES (1,'primary','0','None No','no','no','','','','','','1969-12-31','','','','','','','','','','','','','','1969-12-31',1,'','TRUE',''),(2,'secondary','','','','','','','','','','1969-12-31','','','','','','','','','','','','','','1969-12-31',1,'','TRUE',''),(3,'tertiary','','','','','','','','','','1969-12-31','','','','','','','','','','','','','','1969-12-31',1,'','TRUE','');


INSERT INTO `patient_access_onsite` VALUES (1,1,'First1','$2a$05$scFVRY3F2iAUqzW7gtlP7.u5IziQIPCOQgXsCinprIpm8TXuv7r5K',1,'$2a$05$scFVRY3F2iAUqzW7gtlP7G$');

INSERT INTO `patient_data` VALUES (1,'','','','First','Last','m','2021-02-22','123 Foo St','','Barson','WA','','','','','','','','',0,'','','2021-02-22 00:00:00','Female','','',1,0,'e@mail.com','','','','','','','','','','','','1969-12-31 17:00:00','1',1,'','','','','','','','','','','',0,'','','','','','','','','','','','','','','','','standard','2021-02-22','1969-12-31','NO','1969-12-31','','','','','','','YES',NULL,'',NULL,'',0,'','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

