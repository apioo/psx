REM @ECHO OFF
java -cp "J:/application/bin/saxon/saxon6-5-5/saxon.jar;J:/application/bin/saxon/saxon6-5-5/xslthl.jar" com.icl.saxon.StyleSheet -o I:/www/projects/psx/doc/manual.html I:/www/projects/psx/doc/docbook/manual.xml I:/www/projects/psx/doc/docbook/psx.xsl
for /f %f in ('dir /b I:\www\projects\psx\doc\docbook\packages') do java -cp "J:/application/bin/saxon/saxon6-5-5/saxon.jar;J:/application/bin/saxon/saxon6-5-5/xslthl.jar" com.icl.saxon.StyleSheet -o I:/www/projects/psx/doc/packages/%~nf.html I:/www/projects/psx/doc/docbook/packages/%f I:/www/projects/psx/doc/docbook/psx.xsl
PAUSE
