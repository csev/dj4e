# Django templates

Time to display some data! Django gives us some helpful built-in __template tags__ for that.

## What are template tags?

You see, in HTML, you can't really write Python code, because browsers don't understand it. They know only HTML. We know that HTML is rather static, while Python is much more dynamic.

__Django template tags__ allow us to transfer Python-like things into HTML, so you can build dynamic websites faster. Cool!

## Display post list template

In the previous chapter we gave our template a list of posts in the `posts` variable. Now we will display it in HTML.

To print a variable in Django templates, we use double curly brackets with the variable's name inside, like this:

{% filename %}blog/templates/blog/post_list.html{% endfilename %}
```html
{{ posts }}
```

Try this in your `blog/templates/blog/post_list.html` template. Open it up in the code editor, and replace the existing `<article>` elements with `{{ posts }}`. Save the file, and refresh the page to see the results:

![Figure 13.1](images/step1.png)

As you can see, all we've got is this:

```html
<QuerySet [<Post: My second post>, <Post: My first post>]>
```

This means that Django understands it as a list of objects. Remember from __Introduction to Python__ how we can display lists? Yes, with for loops! In a Django template you do them like this:

{% filename %}blog/templates/blog/post_list.html{% endfilename %}
```html
{% for post in posts %}
    {{ post }}
{% endfor %}
```

Try this in your template.  Make sure to reload your web application and refresh your browser page after every file change.

![Figure 13.2](images/step2.png)

It works! But we want the posts to be displayed like the static posts we created earlier in the __Introduction to HTML__ chapter. You can mix HTML and template tags. Our `body` will look like this:

{% filename %}blog/templates/blog/post_list.html{% endfilename %}
```html
<header>
    <h1><a href="/">Django Girls Blog</a></h1>
</header>

{% for post in posts %}
    <article>
        <time>published: {{ post.published_date }}</time>
        <h2><a href="">{{ post.title }}</a></h2>
        <p>{{ post.text|linebreaksbr }}</p>
    </article>
{% endfor %}
```

{% raw %}Everything you put between `{% for %}` and `{% endfor %}` will be repeated for each object in the list. Refresh your page:{% endraw %}

![Figure 13.3](images/step3.png)

Have you noticed that we used a slightly different notation this time (`{{ post.title }}` or `{{ post.text }}`)? We are accessing data in each of the fields defined in our `Post` model. Also, the `|linebreaksbr` is piping the posts' text through a filter to convert line-breaks into paragraphs.



Works like a charm? We're proud! Step away from your computer for a bit – you have earned a break. :)

![Figure 13.4](images/donut.png)
