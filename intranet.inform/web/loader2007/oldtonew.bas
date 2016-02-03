const crlf="\n"

split command() by "#" to sourcepath
if not isdefined(sourcepath) then
	sourcepath="r:/record/"
	destpath="o:/record/"
endif


open directory sourcepath pattern "*.ord" option SbCollectFiles as 1
	thisord=nextfile(1)
	if isdefined(thisord) then
		while isdefined(thisord)
			fullord=sourcepath&thisord
			'print fullord, crlf
			if filemodifytime(fullord)<(gmtime-180) then
				thisurn=""
				thistime=""
				thisclient=""
				thisservice=""
				'undef filealias

				open fullord for input as 2
				while not eof(2)
					line input #2, ordline
					ordline=chomp(ordline)
					split ordline by ":" to ordleft,ordright,ordextra
					if ordleft="sifurn" then
						thisurn=ordright
					endif
					if ordleft="calltime" then
						thistime=left(ordright,8)
					endif
					if ordleft=";CLIENT" then
						thisclient=ordright
					endif
					if ordleft=";SERVICE" then
						thisservice=ordright
					endif
					'if ordleft=";ALIAS" then
					'	filealias{"."&lower(ordright)}=ordextra
					'endif
				wend
				close 2
				fullsrc=sourcepath
				fulldest=destpath&thistime&"/"&thisclient&"/"&thisservice&"/"
				print fullsrc,crlf
				print "  ",fulldest,crlf
				open directory fullsrc pattern thisurn&".*" option SbCollectFiles as 3
					myfile=nextfile(3)
					if isdefined(myfile) then
print "myfile:",myfile,crlf
						movedfiles=0
						while isdefined(myfile)
							thisfile=lower(myfile)
							if right(thisfile,4)=".tmp" then
								print "Kill>",fullsrc&thisfile,crlf
								delete fullsrc&thisfile
							else
								fileext=lower(right(thisfile,4))
								if fileext<>".ord" then
									ordsrc=fullsrc&thisfile
									'if isempty(filealias{fileext}) then
										orddest=fulldest&thisfile
									'else
									'	orddest=fulldest&thisurn&"."&filealias{fileext}
									'endif
									print "  >",ordsrc,crlf
									print "  >>",orddest,crlf
									mkdir fulldest

									if moveafile(ordsrc, orddest) then
										print "name:",ordsrc, orddest
									endif

									movedfiles=movedfiles+1
								endif
							endif
							myfile=nextfile(3)
						wend
						if movedfiles=0 then
							ordsrc=fullsrc&thisurn&".ord"
							orddest=fulldest&thisurn&".ord"
							'print " C>",ordsrc,crlf
							'print " C>>",orddest,crlf
							mkdir fulldest

							if moveafile(ordsrc, orddest) then
								print "name:",ordsrc, orddest
							endif
						endif
					endif
				close directory 3
			endif
			thisord=nextfile(1)
		wend
	endif
close directory 1

function moveafile(srcmove, destmove)
	destlen=filelen(destmove)
	if isundef(destlen) then
		destlen=0
	endif
	movegood=false
	if destlen=filelen(srcmove) then
		movegood=true
	else
		movegood=false
		print "  =Copy>",destmove,crlf
		on error goto badfile
		filecopy srcmove,destmove
		movegood=true
		goto goodfile
		badfile:
		print "  =Problem>",destmove,crlf
		movegood=false
		goodfile:
		on error goto null
	endif
	stime=filecreatetime(srcmove)
	'print "  =Time>",stime,crlf
	'on error goto null
	set file destmove createtime=stime
	set file destmove modifytime=stime
	'Yield to prevent thrashing live machine!
	'sleep SLEEP_TIME
	delete srcmove
	'print "kill", srcmove, crlf
	'movegood=false
end function
