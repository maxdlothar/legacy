const crlf="\n"
const WAVE_RHET32=1
const WAVE_OKI32=5
import mysql.bas

split command() by "#" to daysback, onlyurgent
if not isdefined(daysback) then
	daysback=21
endif
if not isdefined(onlyurgent) then
	onlyurgent=false
endif
'print daysback, " ", onlyurgent, crlf
'end

'Columns Used In Database
fieldarray{"recordid"}=true
fieldarray{"recorduri"}=true
fieldarray{"serviceid"}=true
fieldarray{"servicesub"}=true
fieldarray{"exportset"}=true
fieldarray{"callcli"}=true
fieldarray{"callddi"}=true
fieldarray{"calltime"}=true
fieldarray{"loadtime"}=true
fieldarray{"exporttime"}=true
fieldarray{"senttime"}=true
fieldarray{"loadagent"}=true
fieldarray{"loadempty"}=true
fieldarray{"loadexclude"}=true
fieldarray{"loadpartial"}=true
fieldarray{"salutation"}=true
fieldarray{"forename"}=true
fieldarray{"surname"}=true
fieldarray{"property"}=true
fieldarray{"street"}=true
fieldarray{"locality"}=true
fieldarray{"town"}=true
fieldarray{"county"}=true
fieldarray{"country"}=true
fieldarray{"postcode"}=true
fieldarray{"phonenum"}=true
fieldarray{"email"}=true

dbh=mysql::RealConnect("192.168.1.209","root","password","loader07")
dba=mysql::RealConnect("192.168.1.209","root","password","loader07")
dayto=gmtime-300
'daycheck=dayto-(86400*14)
daycheck=dayto-86400*daysback
while daycheck<dayto
	daycheck=daycheck+86400
	daystamp=formatdate("YEAR0M0D",daycheck)
'	daystamp="20080818"
	x=scandir(daystamp,"//everyware-d1/record/",WAVE_OKI32)
	x=scandir(daystamp,"//everyware-d2/record/",WAVE_OKI32)
	x=scandir(daystamp,"r:/rec/vacate/",WAVE_RHET32)
wend
if dbh>0 then
	mysql::close(dbh)
endif
if dba>0 then
	mysql::close(dba)
endif
dba=mysql::RealConnect("192.168.1.209","root","password","system")
if dba>0 then
	datacomm="delete from messages where messageservice='Transcription' and messagetitle='Transcription' and messagetime>='"&formatdate("YEAR0M0D",dayto-86400*(daysback-1))&"'"
	print datacomm,crlf
	mysql::query dba,datacomm
	for i=lbound(messlist) to ubound(messlist) step 2
		datacomm="insert into messages set messagetest='0', messagetime='"&messtime{messlist[i]}&"', messageservice='Transcription', messagetitle='Transcription', messagecontent='"&messlist[i+1]&"'"
		print datacomm,crlf
		mysql::query dba,datacomm
		'print "Message:", messlist[i], , crlf
	next i
	mysql::close(dba)
endif

end

'C_Name/C_Sub
function scandir(scanday, scanbase, sourcefiletype)
	'print scanday, scanbase, sourcefiletype, crlf
	sourcetype=sourcename(sourcefiletype)
	desttype=sourcename(WAVE_RHET32)
	on error goto baddir
	'open directory scanbase&scanday pattern "*" option SbCollectDirectories or SbCollectFiles as 1
	open directory scanbase&scanday pattern "" option SbCollectDirectories as 1
	thismain=nextfile(1)
	if isdefined(thismain) then
		print scanbase&scanday,crlf
	endif
	while isdefined(thismain)
		fullroot=scanbase&scanday
		dayroot=mid(scanday, 7, 2)
		fullmain=scanbase&scanday&"/"&thismain
		if isdirectory(fullmain) then
			'print "1",fullmain,crlf
			open directory fullmain pattern "" option SbCollectDirectories as 2
			thissub=nextfile(2)
			while isdefined(thissub)
				fullsub=fullmain&"/"&thissub
				if isdirectory(fullsub) then
					'print "2", fullsub,crlf
					open directory fullsub pattern "*.ord" option SbCollectFiles as 3
					thisord=nextfile(3)
					if isdefined(thisord) then
						undef extname
						on error goto null
						sqlquery="select service,servsub,servicestart,serviceend,oldconvert from services where oldconvert='1' and (service='"&thismain&"' or substring(service,1,8)='"&thismain&"') and servsub='"&thissub&"' and servicestart<='"&scanday&"000000' and serviceend>='"&scanday&"235959'"
						'print sqlquery,crlf
						mysql::query dba,sqlquery
						'print ">", thismain, ":", thissub, crlf
						if mysql::AffectedRows(dba)=1 then
							mysql::FetchHash(dba,subdata)
							'if not isempty(subdata{"oldconvert"}) then
							'if not subdata{"oldconvert"}="1" then
								sqlquery="select * from servicesext where service='"&thismain&"' and servsub='"&thissub&"' and servicestart<='"&scanday&"000000' and serviceend>='"&scanday&"235959'"
								'print sqlquery,crlf
								mysql::query dba,sqlquery
								for extranum=1 to mysql::AffectedRows(dba)
									mysql::FetchHash(dba,extdata)
									extname{extdata{"serviceext"}}=extdata{"oldext"}
								next
							'endif
							print crlf
							while isdefined(thisord)
								fullord=fullsub&"/"&thisord
								preord=replace(thisord, ".ord", "")
								completeord=true
								print "\r>", thismain, ":", thissub, ":", thisord
								'print "---Read Order----",crlf
								undef ordfields
								undef ordused
								open fullord for input as 6
									while not eof(6)
										line input #6, ordline
										'ordline=replace(ordline, chr(13))
										ordline=chomp(ordline)
										if not isempty(ordline) then
											if left(ordline,1)<>";" then
												split ordline by ":" to ordleft,ordright
												ordleft=lower(ordleft)
												if ordleft="sifurn" then
													ordleft="recorduri"
												endif
												if ordleft="partrecord" then
													ordleft="loadpartial"
												endif
												if ordleft="order" then
													ordleft=lower(ordright)
													ordright="1"
												endif
												if lower(ordright)="true" then
													ordright="1"
												endif
												if lower(ordright)="yes" then
													ordright="1"
												endif
												if lower(ordright)="on" then
													ordright="1"
												endif
												'print ordleft," => ",ordright,crlf
												ordfields{ordleft}=ordright
											endif
										endif
									wend
								close 6
								if isdefined(ordfields{"complete"}) then
									'print "File Completed!", crlf
									fileready=true
								else
									if filemodifytime(fullord)<(gmtime-1800) then
										'print "File Completed By Time!", crlf
										fileready=true
									else
										fileready=false
										print "File NOT Completed By Time!", crlf
										completeord=false
									endif
								endif
								ordused{"complete"}=true

								if fileready then
									sqlquery="select recordid,serviceid,servicesub,recorduri,calltime from records where recorduri='"&lower(preord)&"' and serviceid='"&thismain&"' and servicesub='"&thissub&"'"
									'print sqlquery,crlf
									mysql::query dba,sqlquery
									if mysql::AffectedRows(dba)>0 then
'print "F1:",crlf
										mysql::FetchHash(dba,prevrecord)
										insertrecs=false

										thisfield=prevrecord{"calltime"}
										if not isempty(thisfield) then
'print "F2:",prevrecord{"calltime"},crlf
											fileready=false
											'print "Call Already Exists!", crlf
											completeord=false
										endif
									else
										insertrecs=true
									endif
									'Must Be After File Read Data To Ensure Lower Case!
									ordfields{"recorduri"}=lower(ordfields{"recorduri"})
									ordfields{"serviceid"}=lower(thismain)
									ordfields{"servicesub"}=lower(thissub)
								endif

								recordcomm=""

								if fileready then
									for i=lbound(fieldarray) to ubound(fieldarray) step 2
										'print ordfields{fieldarray[i]}, crlf
										thisfield=fieldarray[i]
										if isdefined(ordfields{thisfield}) then
											'if ordfields[i+1]<>undef then
												ordused{thisfield}=true
												'print "Record Entry:",thisfield,"-",ordfields{thisfield},crlf
												firstitem=isempty(recordcomm)
												if firstitem then
													if insertrecs then
														recordcomm="insert into records set"
													else
														recordcomm="update records set"
													endif
												endif
												if thisfield="calltime" then
													recordcalltime=ordfields{thisfield}
												else
													if not firstitem then
														recordcomm=recordcomm&","
													endif
													recordcomm=recordcomm&" "&thisfield&"='"&mysql_real_escape_string(ordfields{thisfield})&"'"
												endif
											'endif
										endif
									next i
									if not insertrecs then
										recordcomm=recordcomm&" where recorduri='"&ordfields{"recorduri"}&"'"
									endif
								endif

								if fileready then
									'datacomm=""
									for i=lbound(ordfields) to ubound(ordfields) step 2
										'print ordfields[i],crlf
										'print ordused{ordfields[i]},crlf
										thisfield=ordfields[i]
										'if isdefined(ordfields{thisfield}) then
											if ordused{thisfield} then
												'Do Nothing-CAN'T DO NOT BECAUSE "not undef==undef"
											else
												if isundef(ordused{thisfield}) then
													if isdefined(ordfields{thisfield}) then
														thisvalue=ordfields{thisfield}
														print crlf,"Data Entry:",thisfield,"-",thisvalue
														datacomm="select addid from additional where recorduri='"&lower(preord)&"' and addfield='"&thisfield&"'"
														'print datacomm,crlf
														mysql::query dba,datacomm
														'print mysql::AffectedRows(dba),crlf
														if mysql::AffectedRows(dba)=0 then
															datacomm="insert into additional set recorduri='"&lower(preord)&"', addfield='"&thisfield&"', addvalue='"&thisvalue&"'"
															'print datacomm,crlf
															mysql::query dba,datacomm
														endif
													endif
												endif
											endif
										'endif
									next i
									print crlf
								endif

								if fileready then
									'if not isempty(subdata{"oldconvert"}) then
									if subdata{"oldconvert"}=1 then
										print crlf,"---Convert Audio----"
										open directory fullsub pattern preord&".*" option SbCollectFiles as 4
										thissnd=nextfile(4)
										while isdefined(thissnd)
											if thisord<>thissnd then
												postsnd=replace(thissnd, preord&".", "")
												thismainshort=left(thismain, 8)
												thissubshort=left(thissub, 8)
												sourcesnd=fullsub&"/"&thissnd
												if not isempty(extname{postsnd}) then
													'       replace(replace(replace(sourcesnd, fullroot, "r:/rec/anaload"), thismain&"/"&thissub&"/"&thissnd,     thismainshort&"/"&thissubshort&"/"&dayroot&"/"&thissnd), "."&postsnd, "."&extname{postsnd})
													destdir=replace(replace(replace(sourcesnd, fullroot, "r:/rec/anaload"), thismain&"/"&thissub&"/"&thissnd, thismainshort&"/"&thissubshort&"/"&dayroot&"/"&thissnd), preord&"."&postsnd, "")
													destsnd=destdir&left(preord, 8)&"."&extname{postsnd}
													print crlf,"Convert >", sourcesnd
													'print "        Dir>", destdir,crlf
													print crlf,"        To >", destsnd
													'print postsnd, extname{postsnd}, crlf
													mkdir destdir
													'open "127.0.0.1:40105" for socket as 5
													open "192.168.1.59:40105" for socket as 5
													print #5, "SOURCE:",sourcesnd,"\r\n"
													print #5, "SOURCETYPE:",sourcefiletype,"\r\n"
													print #5, "DEST:",destsnd,"\r\n"
													print #5, "DESTTYPE:",WAVE_RHET32,"\r\n"
													print #5, "CONVERT:\r\n"
													line input #5, waitok
													'print waitok
													'if left(waitok,1)="+" then
													if left(waitok,1)="+" and fileexists(destsnd) then
														print crlf,"...Convert Happy!"
													else
														completeord=false
														print crlf,"...Convert Sad!"
													endif
													close 5
												else
													completeord=false
													Dummy=badmess(thismain&"/"&thissub&"/"&postsnd, "No Convert ID-"&thismain&"/"&thissub&"/"&postsnd, scanday)
													print crlf,"...Convert Bad (",thismain&"/"&thissub&"/"&postsnd,")! > ",thissnd
												endif
												print crlf
												'print "convsnd /IT:"&sourcetype&" "&"/OT:"&desttype&" "&sourcesnd&" "&destsnd,crlf
												'exreturn=execute("convsnd /IT:"&sourcetype&" "&"/OT:"&desttype&" "&sourcesnd&" "&destsnd, 60, PID)
												'exreturn=execute("convsnd /?", 60, PID)
												'print "exreturn:",exreturn,crlf
											endif
											thissnd=nextfile(4)
										wend
										close directory 4
									endif
								endif
								if completeord then
									print recordcomm,crlf
									mysql::query dba,recordcomm
									'print mysql::AffectedRows(dba),crlf

									if insertrecs then
										recordcomm="select recordid from records where recorduri='"&ordfields{"recorduri"}&"'"
										print recordcomm,crlf
										mysql::query dba,recordcomm
										mysql::FetchHash(dba,lastid)
										insrecordid=lastid{"recordid"}
									else
										insrecordid=prevrecord{"recordid"}
									endif

									'Remove
									'while isdefined(thisord)
										'Remove
										'thisord=nextfile(3)
									'Remove
									'wend

									if isempty(extname{"?r?"}) then
										completeord=(subdata{"oldconvert"}=0)
										if not completeord then
											print "?r? Record ID Missing!",crlf
											Dummy=badmess(thismain&"/"&thissub&"/?r?", "No Record ID (?r?)-"&thismain&"/"&thissub, scanday)
										endif
									else
										destsnd=destdir&left(preord, 8)&"."&extname{"?r?"}
										open destsnd for output as 7
											print #7,insrecordid,".\r\n"
										close 7
									endif

								endif

								if completeord then
									'destsnd=destdir&left(preord, 8)&"."&extname{"?r?"}
									'open destsnd for output as 7
									'	print #7,insrecordid,".\r\n"
									'close 7
									'temp?
									'destsnd=destdir&left(preord, 8)&".rc"
									'open destsnd for output as 7
									'	print #7,insrecordid,".\r\n"
									'close 7

									recordcomm="update records set calltime='"&recordcalltime&"' where recorduri='"&ordfields{"recorduri"}&"'"
									print "Record Complete!",crlf
									'print recordcomm,crlf
									mysql::query dba,recordcomm
								'else
									'print "Record Incomplete!",crlf
								endif

								'Remove-Only Do 1 File Per Directory!
								'if isdefined(thisord) then
									thisord=nextfile(3)
								'Remove-Only Do 1 File Per Directory!
								'endif
							wend
							print crlf
						'else
						'	Dummy=badmess(thismain&"-"&thissub, "No Convert-"&thismain&"/"&thissub, scanday)
						'	print "Bad Service-", thismain, ":", thissub, crlf
						endif
						on error goto baddir
					endif
					close directory 3
				endif
				thissub=nextfile(2)
			wend
			close directory 2
		endif
		thismain=nextfile(1)
	wend
	close directory 1
	baddir:
	on error goto null
	'itemdefined=isdefined(fieldused{ItemUsed})
	'if itemdefined then
	'	fieldused{ItemUsed}=true
	'endif
	'haveused=itemdefined
	scandir=true
end function

function sourcename(thefiletype)
	if thefiletype=WAVE_RHET32 then
		sourcename="RHET32"
	endif
	if thefiletype=WAVE_OKI32 then
		sourcename="OKI32"
	endif
end function

function mysql_real_escape_string(stringtofix)
	stringtofix=replace(stringtofix, "\"", "\\\"")
	stringtofix=replace(stringtofix, "\'", "\\\'")
	stringtofix=replace(stringtofix, "\;", "\\;")
	mysql_real_escape_string=stringtofix
end function

function badmess(messageid, messagetext, messagetime)
	messlist{messageid}=messagetext
	'if isdefined(messtime{messageid}) then
	'	if messagetime<messtime{messageid} then
	'		messtime{messageid}=messagetime
	'	endif
	'else
	'	messtime{messageid}=messagetime
	'endif
	if isundef(messtime{messageid}) then
		messtime{messageid}=messagetime
	endif
end function
