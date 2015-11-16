# MJJ Comment Ratings

This adds a 5 star field to the WordPress comment form, saves the results for each comment submitted then calculates the average rating for a post from all the comments.

The comment form field is the only thing automatically added. To show stored ratings (either post or the individual comment) use :

```php
MJJ_Comment_Ratings::show_star_rating( $rating = 0, $width = 30, $edit = false )
```

where rating is the value of the rating and width is the width of the stars in pixels. (note: find a better way to do this maybe) 

$edit = true allows you to use the field as an input field rather than read only. (eg for editing or moderations purposes. I have hooked it into simple comment editing https://github.com/tharsheblows/simple-comment-editing. )

The metakey for the post average rating is "_mjj_post_rating"
The metakey for the comment individual rating is "_mjj_comment_rating"

This is the first pass at this - I haven't done cross browser testing and there's no fallback for non-js. Also, comments with no rating submitted are zero; I'm not sure that this is ideal. Please don't use this as a finished plugin, it's more a starting point at the moment.
