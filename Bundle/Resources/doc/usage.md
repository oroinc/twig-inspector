# Documentation

## Inspect with built-in Dev Tools in a Browser
The bundle adds HTML comments at the start and the end of Twig blocks and templates to the source of rendered HTML pages:<br/>
![HTML Comments For Inspect Twig templates](./images/html-comments.png)

**Note:** Different line types at the comment prefix used to distinguish start and end comment for different blocks if there are a lot of them on a page. 
They don't have any other special meanings.

Better to view generated comments in the Browser Dev Tools at the "Elements" tab in a Webkit based browsers (Chrome, Safari, Opera etc.) or at the "Inspector" tab in the Firefox, as there comments are well formatted.

We don't recommend to use the source code view as comments there are not enough readable.

## Inspect with Symfony Web Profiler Toolbar
Also the bundle comes with an extension for **Symfony Web Profiler** that helps to debug visible blocks and navigate to templates source code in your favourite IDE or in a browser without any additional actions.

By default the extension is disabled to don't affect page rendering. 
It uses cookies to remember is it enabled on a page reload.

### Using Twig Inspector Toolbar Extension

1. In a Web Profiler Toolbar hover on `</>` icon<br/>
![Web Profiler Toolbar Twig Inspector Extension Icon](./images/toolbar-extension-icon.png)
2. Click on "enable" checkbox. That will reload the page and adds comments to the page source code.
The icon will have an orange background while it's enabled.
3. Click on `</>` icon again. Now it goes green.
4. Hower on the element you want to inspect at the webpage.<br/> 
The element you are hovering will be highlighted with the transparent blue overlay. 
Also it will container the hint with block and template names used for rendering hovered html.<br/>
![Hovered element with enabled Twig Inspector](./images/hovered-element.png)

5. Click on an element.
If only one block or template is used for rendering that block you will instantly be redirected to the IDE.
In case there are multiple blocks and templates used - you will see the popup block with links them. 
Just click on the template you want to be opened.<br/>
![List of used templates](./images/list-templates.png)
6. The template will be opened in your favourite IDE<br/>
![Opened template in an IDE](./images/template-in-ide.png)
 
 If your want to inspect elements that are not visible on a webpage, like hidden or elements from the `header` section, 
 the only option is to [Inspect with built-in Dev Tools in a Browser](#inspect-with-built-in-dev-tools-in-a-browser) 
