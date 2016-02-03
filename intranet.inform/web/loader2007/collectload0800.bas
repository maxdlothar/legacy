const crlf="\n"
const WAVE_RHET32=1
const WAVE_OKI32=5
import mysql.bas

split command() by "#" to daysback, onlyurgent, doingcleanup
if not isdefined(daysback) then
	daysback=35
endif
if not isdefined(onlyurgent) then
	onlyurgent=false
endif
'print daysback, " ", onlyurgent, crlf
'end

dbh=mysql::RealConnect("192.168.1.209","root","password","loader07")
dba=mysql::RealConnect("192.168.1.209","root","password","loader07")
dayto=gmtime-300
'daycheck=dayto-(86400*14)
daycheck=dayto-86400*daysback+1
startstamp=formatdate("YEAR0M0D000000",daycheck+86400)
print startstamp, crlf
while daycheck<dayto
	daycheck=daycheck+86400
'daystamp=formatdate("YEAR0M0D0H0m0s",daycheck)
	daystamp=formatdate("YEAR0M0D",daycheck)
	'print daystamp,crlf
	x=scandir(daystamp,"//everyware-d1/record/")
	x=scandir(daystamp,"//everyware-d2/record/")
	'x=scandir(daystamp,"r:/rec/vacate/",WAVE_RHET32)
wend
if dbh>0 then
	mysql::close(dbh)
endif
if dba>0 then
	mysql::close(dba)
endif

dba=mysql::RealConnect("192.168.1.209","root","password","system")
if dba>0 then
	datacomm="delete from messages where messageservice='Transcription08' and messagetitle='Transcription08' and messagetime>='"&startstamp&"'"
	print datacomm,crlf
	mysql::query dba,datacomm
	for i=lbound(messlist) to ubound(messlist) step 2
		datacomm="insert into messages set messagetest='1', messagetime='"&messtime{messlist[i]}&"', messageservice='Transcription08', messagetitle='Transcription08', messagecontent='"&messlist[i+1]&"'"
		print datacomm,crlf
		mysql::query dba,datacomm
		'print "Message:", messlist[i], , crlf
	next i
	mysql::close(dba)
endif

end

'C_Name/C_Sub
function scandir(scanday, scanbase)
	on error goto baddir
	'open directory scanbase&scanday pattern "*" option SbCollectDirectories or SbCollectFiles as 1
	open directory scanbase&scanday pattern "" option SbCollectDirectories as 1
	thismain=nextfile(1)
	if isdefined(thismain) then
		print scanbase&scanday,crlf
		while isdefined(thismain)
			fullroot=scanbase&scanday
			dayroot=mid(scanday, 7, 2)
			fullmain=scanbase&scanday&"/"&thismain
			if isdirectory(fullmain) then
				'print "1",fullmain,crlf
				open directory fullmain pattern "" option SbCollectDirectories as 2
				thissub=nextfile(2)
				if isdefined(thissub) then
					while isdefined(thissub)
						fullsub=fullmain&"/"&thissub
						if isdirectory(fullsub) then
							'print "2", fullsub,crlf
							open directory fullsub pattern "*.ord" option SbCollectFiles as 3
							thisord=nextfile(3)
							if isdefined(thisord) then
								on error goto null
								sqlquery="select service,servsub,servicestart,serviceend,oldconvert from services where (service='"&thismain&"' or substring(service,1,8)='"&thismain&"') and servsub='"&thissub&"' and servicestart<='"&scanday&"000000' and serviceend>='"&scanday&"235959'"
								'print sqlquery,crlf
								mysql::query dba,sqlquery
								'print ">", thismain, ":", thissub, crlf
								if mysql::AffectedRows(dba)=1 then
									mysql::FetchHash(dba,subdata)
									if subdata{"oldconvert"}="0" then
										if not isdefined(fieldarray{"recorduri"}) then
											sqlquery="describe records"
											'print sqlquery,crlf
											mysql::query dbh,sqlquery
											'print ">", thismain, ":", thissub, crlf
											for fieldcount=1 to mysql::AffectedRows(dbh)
												mysql::FetchHash(dbh,fieldread)
												'print fieldread{"Field"},crlf
												fieldarray{fieldread{"Field"}}=true
											next fieldcount
										endif
										'print crlf
										while isdefined(thisord)
											preord=replace(thisord, ".ord", "")

											print "\r>", thismain, ":", thissub, ":", preord

											sqlquery="select recorduri from records where recorduri='"&preord&"'"
											'print sqlquery,crlf
											mysql::query dbh,sqlquery
											'print ">", thismain, ":", thissub, crlf
											doinginsert=(mysql::AffectedRows(dbh)=0)
											fullord=fullsub&"/"&thisord
											'print "---Read Order----",crlf
			
											fileready=false
											if doinginsert then
												if filemodifytime(fullord)<(gmtime-180) then
													recordquery="insert into records set recorduri='"&preord&"', serviceid='"&thismain&"', servicesub='"&thissub&"'"
													open fullord for input as 6
														while not eof(6)
															line input #6, ordline
															'ordline=replace(ordline, chr(13))
															ordline=chomp(ordline)
															if not isempty(ordline) then
																if left(ordline,1)<>";" then
																	split ordline by ":" to ordleft,ordright
																	'print ordleft," => ",ordright,crlf
																	if lower(ordleft)="complete" then
																		if lower(ordright)="yes" then
																			fileready=true
																		endif
																	else
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
																		if fieldarray{ordleft} then
																			print "Record:",ordleft,"=",ordright,crlf
																			if ordleft<>"recorduri" then
																				recordquery=recordquery&", "&ordleft&"='"&ordright&"'"
																			endif
																		else
																			sqlquery="select recorduri from additional where recorduri='"&preord&"' and addfield='"&ordleft&"'"
																			'print sqlquery,crlf
																			mysql::query dbh,sqlquery
																			'print ">", thismain, ":", thissub, crlf
																			if mysql::AffectedRows(dbh)=0 then
																				'"select "
																				'mysql::FetchHash(dba,subdata)
																				sqlquery="insert into additional set recorduri='"&preord&"', addfield='"&ordleft&"', addvalue='"&ordright&"'"
																				print sqlquery,crlf
																				mysql::query dbh,sqlquery
																			'else
																			'	print "=Addit:",ordleft,"=",ordright,crlf
																			endif
																		endif
																	endif
																endif
															endif
														wend
													close 6
													if not fileready then
														if filemodifytime(fullord)<(gmtime-900) then
															'print "File Completed By Time!", crlf
															fileready=true
														endif
													endif
												endif
											endif

											'completeord=true
	
											if doinginsert or doingcleanup then
												movegood=fileready
												if fileready then
													'print crlf,"---Move Files---",crlf
													destdir=replace(fullsub, scanbase, "o:/")
													'replace(replace(, thismain&"/"&thissub&"/"&thissnd, thismainshort&"/"&thissubshort&"/"&dayroot&"/"&thissnd), preord&"."&postsnd, "")
													open directory fullsub pattern preord&".*" option SbCollectFiles as 4
													thissnd=nextfile(4)
													while isdefined(thissnd)
														if thisord<>thissnd then
															sourcesnd=fullsub&"/"&thissnd
															destsnd=destdir&"/"&thissnd
															call syncfile
														endif
														thissnd=nextfile(4)
													wend
													close directory 4
												endif
												if movegood then
													sourcesnd=fullsub&"/"&preord&".ord"
													destsnd=destdir&"/"&preord&".ord"
													call syncfile
												endif
												if movegood then
													print recordquery,crlf
													mysql::query dbh,recordquery
												endif
											endif
											thisord=nextfile(3)
										wend
										print crlf
									endif
								else
									Dummy=badmess(thismain&"-"&thissub, "No Service-"&thismain&"/"&thissub, scanday)
									print "Bad Service-", thismain, ":", thissub, crlf
								endif
								on error goto baddir
							else
								print "Remove Dir:",fullsub,crlf
								delete fullsub
							endif
							close directory 3
						endif
						thissub=nextfile(2)
					wend
				else
					print "Remove Dir:",fullmain,crlf
					delete fullmain
				endif
				close directory 2
			endif
			thismain=nextfile(1)
		wend
	else
		print "Remove Dir:",scanbase&scanday,crlf
		delete scanbase&scanday
	endif
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

sub syncfile
	print sourcesnd,crlf
	'if filemodifytime(sourcesnd)<gmtime-86400 then
	if filemodifytime(sourcesnd)<(gmtime-(86400*40)) then
		destlen=filelen(destsnd)
		if isundef(destlen) then
			destlen=0
		endif
		if destlen>=filelen(sourcesnd) then
			print " =Remove File",crlf
			delete sourcesnd
		else
			print "CANNOT FIND DESTINATION FILE! SOURCE NOT REMOVED!"
		endif
	else
		destlen=filelen(destsnd)
		if isundef(destlen) then
			destlen=0
		endif
		if destlen<filelen(sourcesnd) then
			print "  =>",destsnd,crlf
			on error goto badfile
			filecopy sourcesnd,destsnd
			stime=filecreatetime(sourcesnd)
			'print "  =Time>",stime,crlf
			'on error goto null
			set file destsnd createtime=stime
			set file destsnd modifytime=stime
			goto goodfile
			badfile:
			print "  =Problem>",destsnd,crlf
			goodfile:
			on error goto null
		endif
	
		destlen=filelen(destsnd)
		if isundef(destlen) then
			destlen=0
		endif
		if destlen<filelen(sourcesnd) then
			movegood=false
		endif
	endif
end sub

function badmess(messageid, messagetext, messagetime)
'print sqlquery,crlf
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



'-''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
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
		Dummy=badmess(thismain&"-"&postsnd, "No Convert ID-"&thismain&"/"&thissub&"/"&postsnd, scanday)
		print crlf,"...Convert Bad (",thismain&"/"&thissub&"/"&postsnd,")! > ",thissnd
	endif
	print crlf
	'print "convsnd /IT:"&sourcetype&" "&"/OT:"&desttype&" "&sourcesnd&" "&destsnd,crlf
	'exreturn=execute("convsnd /IT:"&sourcetype&" "&"/OT:"&desttype&" "&sourcesnd&" "&destsnd, 60, PID)
	'exreturn=execute("convsnd /?", 60, PID)
	'print "exreturn:",exreturn,crlf
endif
