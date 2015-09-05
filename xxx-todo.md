I have tried to keep commits very clean to show what has changed step by step. Below some description... Each point should match commit.
This PR may be big, so it may be worth to read changes commit by commit instead of all at once.
Please split discussion into two parts - first do we like changes described below and implemented, and then, if we agreed about changes, I will fill the docs and review of the code itself may begin.

1. **Introduce FixerFactory**
It's a class that will register build in fixers, allow to register custom fixers, sort them and attach config to them (see ConfigAwareInterface).
In future (see next changes in this PR) it will also allow to configure fixers.

2. **Fixer::getLevelAsString is no longer static**
In non-tests code method is currently run in instance context and there is no way to mock static method.

3. **Stop mixing level from config file and fixers from CLI arg when one of fixers has dash**
For now we can have:
`--fixers=fixer1,fixer2,fixer3,fixer4,fixer5,fixer6,fixer7,fixer8`
which creates a group of 8 fixers (and ignore level set in config file)
and
`--fixers=fixer1,fixer2,fixer3,fixer4,-fixer5,fixer6,fixer7,fixer8`
which creates a group of fixers in the following steps:
  - get all fixers for level in config file (**unlike previously**!) or default one,
  - add 7 fixers from `--fixers` arg
  - remove fixer5 (from `--fixers` arg)
This behavior is very confusing and has so few user scenarios. The whole behavior changed when one add dash to fixers list.
So let us stop mixing the fixers configuration from CLI args and from config file.
One can still use `--level=psr2 --fixers=strict,-braces` to create target group of fixers from psr2 plus strict minus braces,
same with config file, but cannot mix fixers from CLI arg with level from some magic place.

4. **Replace level and fixers from configuration in favor of rules, remove getLevel from FixerInterface**
Closes #866 (ping @Seldaek).
Instead of setting level and fixers start setting rules. Many rules can be grouped in sets.
Eg to take all psr2 fixers, remove braces fixer and add strict fixer we can use:
CLI args:
`--rules=@PSR2,-braces,strict`
config file:
    ```php
    ->setRules([
        '@PSR2' => true,
        'braces' => false,
        'strict' => true,
    ])
    ```
One set can include other set (like `@Symfony` is `@PSR2` + extra fixers, `@PSR2` is `@PSR1` + other fixers).
When setting the rules set expand to it's definition and adding/removing fixers is doing step by step (so one may exclude set and then add one fixer from it).
For example, consider the following:
    ```
    @PSR1 = encoding,short_tag
    @PSR2 = @PSR1,braces,elseif
    @Symfony = @PSR2,return

    // then, one uses:
    --rules=strict,@Symfony,-@PSR2,@PSR1,-encoding
    // which expanded step by step into:
    --rules=strict,@PSR2,return,-@PSR2,@PSR1,-encoding
    --rules=strict,return,@PSR1,-encoding
    --rules=strict,return,encoding,short_tag,-encoding
    --rules=strict,return,short_tag
    ```

Also, remove getLevel method from FixerInterface.

5. **TODO: Add FixerInterface::configure method**
For now we have only way to turn fixer on or off, without possibility to configure how it should fix the code. This problem manifested first time with HeaderCommentFixer, when we have no way to configure the header itself!
Now, the fixers may have configuration (eg PhpUnitStrictFixer) and we have a way to change default configuration.
Setting the configuration for Fixers when use CLI tool is only possible in .php_cs file, not via CLI args.
In .php_cs file configuration is passed as part of rules definition:
    ```php
    ->setRules([
        '@PSR2' => true,
        'braces' => false,
        'strict' => true,
        'php_unit_strict' => [ 'assertEquals', 'assertNotEquals' ],
    ])
    ```
When a rule is false then fixer is off, when rule is truly then rule is on - especially when true it is on with default configuration and when it's array it is on and array is passed to configure method.

Also, remove Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader method
TODO - PHPUnit... ??

6. **TODO: Cache now detect configuration change not only by tool version and names of fixers, but also their configuration**
Closes #1305.

7. **TODO: Remove splitting fixer files into level subdirectories**

8. **TODO: Mark fixers as risky and run them only with explicity willing**
Closes #942.

9. **TODO: Add ability to mark fixers as conflicting**
Closes #910 and #1033.

10. **TODO: UpdateConfigInterface**
