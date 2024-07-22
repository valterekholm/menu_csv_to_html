# menu_csv_to_html
Code to convert a csv-like text for restaurant menu into html table

The text-file is for the data - dishes etc, divided line-wise.
A line starting with ** is a header, * a subheader. Others are dishes...
empty lines are ignored.

Parantheses in header gets marked as "span".
The optional dish-description gets marked by classname "infotext"

A dish-line must have this order [number name price description], separated by ;
If number-column is ommited, the price mark ":-" is not showing in output.

The code is tested just with the kind of data in csv.txt (separate file)

It has some variations:

header, subheader, dish with number, name, price, description
**Förrätter**
*Serveras med tamarind chatney, mango, mynta och raita*
1;Papadam;39;Två skivor krispigt linsbröd med mango-chutney

header, dish without description
**Birayani Rätter**
16;Chicken/Lamm/Veg;169/189/159



2024-07-22
The aim was to offer the developer to use various html-cominations (like table, divs) to print the menu, but so far I have just table.
