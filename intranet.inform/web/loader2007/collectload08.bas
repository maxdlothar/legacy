const crlf="\n"
const WAVE_RHET32=1
const WAVE_OKI32=5
const SLEEP_TIME=1
import mysql.bas

split command() by "#" to daysback, onlyurgent, doingcleanup, oneclient
if not isdefined(daysback) then
	daysback=35
endif
if isdefined(onlyurgent) then
	onlyurgent=(onlyurgent="1")
else
	onlyurgent=false
endif
'print daysback, " ", onlyurgent, crlf
'end
'verbose=true

dbh=mysql::RealConnect("192.168.1.209","root","password","loader07")
dba=mysql::RealConnect("192.168.1.209","root","password","loader07")
dayto=gmtime-300
daycheck=dayto-86400*daysback+1
startstamp=formatdate("YEAR0M0D000000",daycheck+86400)
print startstamp, crlf
while daycheck<dayto
	daycheck=daycheck+86400
	daystamp=formatdate("YEAR0M0D",daycheck)
	x=scandir(daystamp,"j:/urgent2009/")
	if not onlyurgent then
		x=scandir(daystamp,"j:/record2009/")
	endif
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
	open directory scanbase&scanday pattern "" option SbCollectDirectories as 1
	thismain=nextfile(1)
	if isdefined(thismain) then
		print scanbase&scanday,crlf
		while isdefined(thismain)
			fullroot=scanbase&scanday
			dayroot=mid(scanday, 7, 2)
			fullmain=scanbase&scanday&"/"&thismain
			if isdirectory(fullmain) then
				open directory fullmain pattern "" option SbCollectDirectories as 2
				thissub=nextfile(2)
				if isdefined(thissub) then
					while isdefined(thissub)
						fullsub=fullmain&"/"&thissub
						if isdirectory(fullsub) then
							sqlquery="select service,servsub,servicestart,serviceend,oldconvert,isurgent from services where (service='"&thismain&"' or substring(service,1,8)='"&thismain&"') and servsub='"&thissub&"' and servicestart<='"&scanday&"000000' and serviceend>='"&scanday&"235959'"
							DoneAnUrgent=false
							mysql::query dba,sqlquery
							if mysql::AffectedRows(dba)=1 then
								'mysql::FetchHash(dba,subdata)
								'if onlyurgent then
								'	getthisdir=(subdata{"isurgent"}="1")
								'else
									getthisdir=true
								'endif
								if subdata{"oldconvert"}="1" then
									getthisdir=false
								endif
							else
								Dummy=badmess(thismain&"-"&thissub, "No Service-"&thismain&"/"&thissub, scanday)
								print thismain&"/"&thissub,"...BAD SERVICE...",crlf
								getthisdir=false
							endif

							if getthisdir then
								if isdefined(oneclient) then
									dothis=instr(oneclient,thismain)
									if dothis then
									else
										getthisdir=false
									endif
								endif
							endif

							if getthisdir then
								sleep SLEEP_TIME
								open directory fullsub pattern "*.ord" option SbCollectFiles as 3
								thisord=nextfile(3)
								if isundef(thisord) then
									print "Remove Dir:",fullsub,crlf
									close directory 3
									delete fullsub
								endif
							endif

							if getthisdir and isdefined(thisord) then
								on error goto null
								print ">>", thismain, ":", thissub, crlf
								if not isdefined(fieldarray{"recorduri"}) then
									sqlquery="describe records"
									mysql::query dbh,sqlquery
									for fieldcount=1 to mysql::AffectedRows(dbh)
										mysql::FetchHash(dbh,fieldread)
										fieldarray{fieldread{"Field"}}=true
									next fieldcount
								endif
								while isdefined(thisord)
									preord=replace(thisord, ".ord", "")

									print "\r>", thismain, ":", thissub, ":", preord,crlf

									sqlquery="select recorduri from records where recorduri='"&preord&"'"
									mysql::query dbh,sqlquery
									doinginsert=(mysql::AffectedRows(dbh)=0)
									fullord=fullsub&"/"&thisord
									'print "---Read Order----",crlf
	
									fileready=false
									undef filealias
									if doinginsert then
										if filemodifytime(fullord)<(gmtime-180) then
											recordquery="insert into records set recorduri='"&preord&"', serviceid='"&thismain&"', servicesub='"&thissub&"'"
											open fullord for input as 6
												while not eof(6)
													line input #6, ordline
													ordline=chomp(ordline)
													if not isempty(ordline) then
														split ordline by ":" to ordleft,ordright,ordextra
														if left(ordline,1)=";" then
															if ordleft=";ALIAS" then
																filealias{"."&lower(ordright)}=ordextra
															endif
														else
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

																if ordleft="loadurgent" then
																	DoneAnUrgent=true
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
																	if ordleft<>"serviceid" then
																	if ordleft<>"servicesub" then
																		querydata{ordleft}=ordright
																	endif
																	endif
																	endif
																else
																	sqlquery="select recorduri from additional where recorduri='"&preord&"' and addfield='"&ordleft&"'"
																	mysql::query dbh,sqlquery
																	if mysql::AffectedRows(dbh)=0 then
																		sqlquery="insert into additional set recorduri='"&preord&"', addfield='"&ordleft&"', addvalue='"&ordright&"'"
																	else
																		sqlquery="update additional set addvalue='"&ordright&"' where recorduri='"&preord&"' and addfield='"&ordleft&"'"
																	endif
																	print sqlquery,crlf
																	mysql::query dbh,sqlquery
																endif
															endif
														endif
													endif
												wend
											close 6
											if not fileready then
												'Was 900 (15mins)
												if filemodifytime(fullord)<(gmtime-86400) then
													'print "File Completed By Time!", crlf
													fileready=true
												endif
											endif
										endif
									endif

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
													fileext=lower(replace(thissnd,preord,""))
													print "Extension:",fileext,crlf
													if isempty(filealias{fileext}) then
														destsnd=destdir&"/"&thissnd
													else
														destsnd=destdir&"/"&preord&"."&filealias{fileext}
													endif
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
											for qi=lbound(querydata) to ubound(querydata) step 2
												recordquery=recordquery&", "&querydata[qi]&"='"&querydata[qi+1]&"'"
											next qi

											print recordquery,crlf
											mysql::query dbh,recordquery
										endif
										'if subdata{"isurgent"}="1" then
										'	dummy=pingurgent()
										'endif
									endif
									undef querydata
									thisord=nextfile(3)
								wend
								print crlf
								close directory 3
								if DoneAnUrgent then
									dummy=pingurgent()
								endif
							endif
							on error goto baddir
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
	scandir=true
end function

function pingurgent
	postdata="message=URGENT:YES&port=30001"

	data="POST http://192.168.1.209/tools/broadcast HTTP/1.0\r\n"
	data=data&"Host: 192.168.1.209\r\n"
	data=data&"Content-type: application/x-www-form-urlencoded\r\n"
	data=data&"Content-length: "&len(postdata)&"\r\n"
	data=data&"\r\n"

	open "intranet.inform:80" for socket as 5
	
	print #5, data&postdata
	
	waitok=""
	while waitok<>"\r\n"
		line input #5, waitok
		'print ">>>",waitok,crlf
	wend
	
	line input #5, waitok
	'print waitok,crlf
	close 5
	
	'if left(waitok,1)="+" then
	'	print "Ping Agent...",crlf
	'else
	'	print "Ping Fail...",crlf
	'endif
end function

sub syncfile
	print sourcesnd,crlf
	print " >> ",destsnd,crlf
'	movegood=false
'end sub
'
'sub usedtobehere
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
			print "CANNOT FIND DESTINATION FILE! SOURCE NOT REMOVED!",crlf
			movegood=false
		endif
	else
		destlen=filelen(destsnd)
		if isundef(destlen) then
			destlen=0
		endif
		if destlen<filelen(sourcesnd) then
			print "  =Copy>",destsnd,crlf
			on error goto badfile
			filecopy sourcesnd,destsnd
			stime=filecreatetime(sourcesnd)
			'print "  =Time>",stime,crlf
			'on error goto null
			set file destsnd createtime=stime
			set file destsnd modifytime=stime
			'Yield to prevent thrashing live machine!
			'sleep SLEEP_TIME
			goto goodfile
			badfile:
			print "  =Problem>",destsnd,crlf
			movegood=false
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
	messlist{messageid}=messagetext
	if isundef(messtime{messageid}) then
		messtime{messageid}=messagetime
	endif
end function
