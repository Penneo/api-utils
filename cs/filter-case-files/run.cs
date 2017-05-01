using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Pennneo
{
    public class FilterCaseFiles
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
            var input = new QueryInput
            {
                Page = 1,
                PerPage = 10
            };
            input.AddCriteria("createdAfter", new DateTime(2015,10,21));
            // input.AddCriteria("folderIds", 1);
            var response = Query.FindBy<CaseFile>(input);

            // Check empty response
            if (response == null) {
                Console.WriteLine("Nothing found");
                return;
            }

            // Fetch all pages
            do {
                // Check empty response
                if (!response.Success || response.Objects.Count() == 0) {
                    break;
                }

                // Print data in this page
                Console.WriteLine("Page # " + response.Page);
                foreach (CaseFile c in response.Objects)
                {
                    Console.WriteLine("- " + c.Id + " : " + c.Title);
                }
            } while ((response = Query.GetNextPage<CaseFile>(response)) != null);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            // if (severity >= LogSeverity.Error)
            // {
                Debug.WriteLine(severity + ": " + message);
            // }
        }
    }
}
