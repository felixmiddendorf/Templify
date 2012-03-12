# Name
Templify - the native php5 template engine.

# Version
0.3

# Project Home
Templify's home can be found on [Google Code][home]

# Synopsis
    // in controller
    $t = new Templify();
    $t->assign('headline', 'Templify');
    // in template file
    <h1><?= $headline; ?></h1>
    // or
    <h1><?php echo $headline; ?></h1>

# Description
Please refer to the [project home][home] and the examples in `/examples/` for
further information on how to use Templify.

# Author
Written and maintained by [Felix Middendorf][felixmiddendorf]

# Reporting Bugs
Please report any issues to the [Templify issue tracker][issue] on Google Code.

# Copyright
Copyright 2007-2010 Felix Middendorf. All rights reserved. Templify is released
under GNU Lesser Public License (see COPYING.LESSER). Please respect copyright
and license when using Templify.

[home]: http://templify.googlecode.com
[issue]: http://code.google.com/p/templify/issues/entry
[felixmiddendorf]: http://www.felixmiddendorf.eu
