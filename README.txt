Hello!

Your goal is to build an Internet bot (also known as web spider) that will be able to find all the stores belonging to a given supermarket or retailer.

GOAL: You have to create a bot for Dick's Sporting Goods. The web page to get the stores is: http://www.dickssportinggoods.com/storeLocator/index.jsp
You have to create a program written in PHP which will crawl the website in order to find all the stores belonging to this retailer. It will also have to save all the information in an XML file.

To do this test, you should have the following skills:
- you know how to program in PHP
- you know how to use regular expressions to get information from a string
- you have an understanding of the basic technologies used on the Internet (HTTP requests, GET/POST parameters, HTML files, JSON files)

Please see attached a zipped package containing:
- 2 bots written in PHP: basic.php and truevalue.php
- a few PHP classes used by the bots. You may use them if you want.
- a template/skeleton of a bot. You may use it to create your bot if you want.
- the 3 XML files that the bots generated on my computer.
- the PDF manual giving more details about the rules that must be respected and the structure of the XML output files

The bots have 2 input parameters:
1/ the XML file path (where the bot will write the output XML file) 
and
2/ the timeout (if this time is exceeded, the bot should stop downloading web pages and save all the stores it has found so far).


To launch a bot, you can write in the console:
C:\Program Files\PHP\v5.3\php.exe -f C:\...\basic.php C:\...\basic.xml 5
It will launch the bot for basic, and create the output basic.xml. The timeout in this case is 5 minutes.


Good luck!









