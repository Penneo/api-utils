// DOESN'T WORK : This functionality is not supported form the .net sdk

using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Pennneo
{
    public class LinkFolders
    {
        public static void Main(string[] args)
        {
            if (args.Length != 3) {
                Console.WriteLine("Parameters required: endpoint, key, secret");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run();
        }

        public static void run()
        {
            var folder1 = new Folder();
            folder1.Title = "A";
            folder1.Persist();

            var folder2 = new Folder();
            folder2.Title = "B";
            folder2.ParentFolder = folder1;
            folder2.Persist();
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            Debug.WriteLine(severity + ": " + message);
        }
    }
}
