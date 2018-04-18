using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class CaseFileTypes
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
            var caseFile = new CaseFile();

            var availableTemplates = caseFile.GetTemplates().Objects;

            var template = availableTemplates.First();

            foreach (DocumentType documentType in template.DocumentTypes)
            {
                Console.WriteLine(documentType.Id + " : " + documentType.Name);
                foreach(SignerType signerType in documentType.SignerTypes)
                {
                    Console.WriteLine("  |_ " + signerType.Id + " : " + signerType.Role);
                }
                Console.WriteLine("");
            }
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            // Console.WriteLine(severity + ": " + message);
        }
    }
}
