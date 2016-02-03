const crlf="\n"
const WAVE_RHET32=1
const WAVE_OKI32=5
const SLEEP_TIME=1
'import mysql.bas

split command() by "#" to daysback, batfile
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

dayto=gmtime
'daycheck=dayto-(86400*14)
daycheck=dayto-86400*daysback+1
startstamp=formatdate("YEAR0M0D000000",daycheck+86400)
'print startstamp, crlf
while daycheck<dayto
	daycheck=daycheck+86400
'daystamp=formatdate("YEAR0M0D0H0m0s",daycheck)
	daystamp=formatdate("YEAR0M0D",daycheck)
	'print daystamp,crlf
	'exeresult=execute(batfile+" "+daystamp, -1, PID)
	exeresult=execute("cmd /q /c "&batfile&" "&daystamp, -1, PID)
	'x=runbat(daystamp,"//everyware-d1/record/")
wend

end

'C_Name/C_Sub
function runbat(scanday, scanbase)
	print scanday&"/"&scanbase,"...BAD SERVICE...",crlf

	runbat=true
end function
